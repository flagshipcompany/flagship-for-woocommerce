<?php

namespace FS\Components\Shipping\Controller;

use FS\Components\AbstractComponent;
use FS\Components\Shipping\Object\Shipping;
use FS\Components\Web\RequestParam as Req;
use FS\Context\ApplicationContext as Context;
use FS\Components\Shipping\Object\Order;
use FS\Injection\I;

class MetaboxController extends AbstractComponent
{
    public static $flagshipUrl = 'https://smartship-ng.flagshipcompany.com';

    public static $tokenMissingMessage = 'FlagShip API token missing. Please create a FlagShip API token and save in the plugin settings.<br/>';

    public static $noRatesMessage = 'Flagship Shipping has some difficulty in retrieving the rates. Please contact site administrator for assistance.<br/>';

    public static $tokenInvalidMessage = 'Flagship Shipping has some difficulty in retrieving the rates. Please make sure the FlagShip API token is valid.<br/>';

    public function display(Req $request, Context $context, Shipping $shipping)
    {
        $shipment = $shipping->getShipment();
        $service = $shipping->getService();

        // shipment created
        if ($shipment->isCreated()) {
            return $context->render('meta-boxes/order-flagship-shipping-actions', [
                'type' => 'created',
                'shipment' => $shipment->toArray(),
            ]);
        }

        if ($shipment->isExported()) {
            $exportedShipmentId = $shipment->getExportedShipmentId();
            $exportedShipment = $this->getShipment($context, $exportedShipmentId);
        }

        if (!empty($exportedShipment)) {
            $shipmentStatus = $exportedShipment['status'];
            $urlAction = in_array($shipmentStatus, ['dispatched', 'manifested', 'cancelled']) ? 'overview' : 'convert';

            return $context->render('meta-boxes/order-flagship-shipping-actions', [
                'type' => 'exported',
                'exportedShipmentId' => $exportedShipmentId,
                'shipmentUrl' => self::$flagshipUrl.'/shipping/'.$exportedShipmentId.'/'.$urlAction,
            ]);
        }

        $payload = [];

        // quoted but no shipment created
        if (!$shipment->isCreated() && $shipping->isFlagShipRateChoosen()) {
            $payload['type'] = 'create';
            $payload['service'] = $service;
            $payload['cod'] = [
                'currency' => strtoupper(\get_woocommerce_currency()),
            ];
        }

        // possibly not quoted with FS
        if (!isset($payload['type'])) {
            $payload['type'] = 'unavailable';
        }

        // requotes
        if ($requotes = $shipping->getOrder()->getAttribute('flagship_shipping_requote_rates')) {
            $payload['requote_rates'] = $requotes;
        }

        $payload['shipping_options'] = $shipment->getShippingOptions();
        $payload['signature_required'] = isset($payload['shipping_options']['signature_required']) && $payload['shipping_options']['signature_required'] === true ? 'yes' : 'no';

        $context->render('meta-boxes/order-flagship-shipping-actions', $payload);
    }

    public function createShipment(Req $request, Context $context, Shipping $shipping)
    {
        $shipment = $shipping->getShipment();

        if($shipment->isInternational()){
            $hsCodeFlag = $this->getHSCodeFlag($shipping->getOrder());
        }

        //Un-comment on Aug 1
        // if(!$hsCodeFlag) {
        //     $context->alert()->error('Some products in the order do not have HS code. Please enter product HS code to confirm shipment');
        // }

        if ($shipment->isCreated()) {
            $context->alert()->warning('You have a dispatched flagship shipment for this order. FlagShip ID (%s)', [$shipment->getId()]);

            return $this;
        }

        if (!$context->option('token')) {
            $context->alert()->error(self::$tokenMissingMessage);

            return;
        }

        $order = $shipping->getOrder();
        $instanceId = $this->getInstanceId($order->native()->get_address('shipping'));
        $options = $context->option();

        if ($instanceId) {
            $options->sync($instanceId);
        }

        $factory = $context
            ->_('\\FS\\Components\\Shipping\\Request\\Factory\\ShoppingOrderConfirmation');

        $headers = $this->makeOrderHeaders($order);

        $response = $context->command()->confirm(
            $context->api(),
            $factory->setPayload([
                'shipping' => $shipping,
                'request' => $request,
                'options' => $options,
            ])->getRequest(),
            $headers
        );

        if (!$response->isSuccessful()) {
            return;
        }

        $order->removeAttribute('flagship_shipping_requote_rates');

        $shipment->set($response->getContent());

        $shipping->save([
            'save_meta_keys' => true,
        ]);
    }

    public function voidShipment(Req $request, Context $context, Shipping $shipping)
    {
        $shipment = $shipping->getShipment();

        if (!$shipment->isCreated()) {
            $context->alert()->warning('Unable to access shipment with FlagShip ID (%s)', [$shipment->getId()]);

            return;
        }

        if (!$context->option('token')) {
            $context->alert()->error(self::$tokenMissingMessage);

            return;
        }

        $order = $shipping->getOrder();

        $response = $context->api()->delete('/ship/shipments/'.$shipment->getId());

        if (!$response->isSuccessful()) {
            $context->alert()->warning('Unable to void shipment with FlagShip ID (%s)', [$shipment->getId()]);

            return;
        }

        if (!$shipment->hasPickup()) {
            $order->removeAttribute('flagship_shipping_raw');

            return;
        }

        $this->voidPickup($request, $context, $shipping);

        $order->removeAttribute('flagship_shipping_raw');
    }

    public function requoteShipment(Req $request, Context $context, Shipping $shipping)
    {
        if (!$context->option('token')) {
            $context->alert()->error(self::$tokenMissingMessage);

            return;
        }

        $factory = $context
            ->_('\\FS\\Components\\Shipping\\Request\\Factory\\ShoppingOrderRate');
        $rateProcessorFactory = $context
            ->_('\\FS\\Components\\Shipping\\RateProcessor\\Factory\\RateProcessorFactory');

        $order = $shipping->getOrder();
        $instanceId = $this->getInstanceId($order->native()->get_address('shipping'));
        $options = $context->option();

        if ($instanceId) {
            $options->sync($instanceId);
        }

        $response = $context->command()->quote(
            $context->api(),
            $factory->setPayload([
                'shipping' => $shipping,
                'request' => $request,
                'options' => $options,
            ])->getRequest()
        );

        if (!$response->isSuccessful()) {
            $errorMsg = $response->getStatusCode() === 403 ? self::$tokenInvalidMessage : self::$noRatesMessage;
            $context->alert()->error($errorMsg);

            return;
        }

        $service = $shipping->getService();

        $rates = $response->getContent();

        $rates = $rateProcessorFactory
            ->resolve('ProcessRate')
            ->getProcessedRates($rates, [
                'factory' => $rateProcessorFactory,
                'options' => $context->option(),
                'instanceId' => $service['instance_id'] ? $service['instance_id'] : false,
                'methodId' => $context->setting('FLAGSHIP_SHIPPING_PLUGIN_ID'),
            ]);

        $wcShippingRates = [];

        foreach ($rates as $rate) {
            $wcShippingRates[$rate['id']] = $rate['label'].' $'.$rate['cost'];
        }

        if ($wcShippingRates) {
            $shipping->getOrder()->setAttribute('flagship_shipping_requote_rates', $wcShippingRates);
        }
    }

    public function exportShipment(Req $request, Context $context, Shipping $shipping)
    {
        $shipment = $shipping->getShipment();

        if ($shipment->isCreated()) {
            $context->alert()->warning('You have a dispatched FlagShip shipment for this order. FlagShip ID (%s)', [$shipment->getId()]);

            return $this;
        }

        if (!$context->option('token')) {
            $context->alert()->error(self::$tokenMissingMessage);

            return;
        }

        $order = $shipping->getOrder();

        $headers = $this->makeOrderHeaders($order);

        $factory = $context
            ->_('\\FS\\Components\\Shipping\\Request\\Factory\\ShoppingOrderRate');
        $instanceId = $this->getInstanceId($order->native()->get_address('shipping'));
        $options = $context->option();

        if ($instanceId) {
            $options->sync($instanceId);
        }

        $response = $context->command()->prepare(
            $context->api(),
            $factory->setPayload([
                'shipping' => $shipping,
                'request' => $request,
                'options' => $options,
            ])->getRequest(),
            $headers
        );

        if (!$response->isSuccessful()) {
            return;
        }

        $responseContent = $response->getContent();
        $shipment->set([
            'flagship_id' =>  $responseContent['id'],
            'exported' => true,
        ]);

        $shipping->save([
            'save_meta_keys' => true,
        ]);
    }

    public function schedulePickup(Req $request, Context $context, Shipping $shipping)
    {
        $shipment = $shipping->getShipment();

        if (!$shipment->isCreated()) {
            return;
        }

        if (!$context->option('token')) {
            $context->alert()->error(self::$tokenMissingMessage);

            return;
        }

        $factory = $context
            ->_('\\FS\\Components\\Shipping\\Request\\Factory\\ShoppingOrderPickup');

        $response = $context->command()->pickup(
            $context->api(),
            $factory->setPayload([
                'options' => $context->option(),
                'shipping' => $shipping,
                'date' => $request->request->get('flagship_shipping_pickup_schedule_date', date('Y-m-d')),
            ])->getRequest()
        );

        if (!$response->isSuccessful()) {
            $context->alert()->warning('Unable to schedule pick-up with FlagShip ID (%s)', [$shipment->getId()]);

            return;
        }

        $shipment->set('pickup', $response->getContent()[0]);

        $shipping->save();
    }

    public function voidPickup(Req $request, Context $context, Shipping $shipping)
    {
        $pickup = $shipping->getPickup();

        if (!$pickup->isCreated()) {
            return;
        }

        if (!$context->option('token')) {
            $context->alert()->error(self::$tokenMissingMessage);

            return;
        }

        $response = $context->api()->delete('/pickups/'.$pickup->getId());

        if (!$response->isSuccessful()) {
            $context->alert()->warning('Unable to void pick-up with FlagShip Pickup ID (%s)', [$pickup->getId()]);

            return;
        }

        $shipping->getShipment()->remove('pickup');

        $shipping->save();
    }

    protected function getShipment(Context $context, $shipmentId)
    {
        if (!$context->option('token')) {
            return;
        }

        $response = $context->api()->get('/ship/shipments/'.$shipmentId);

        if (!$response->isSuccessful()) {
            return;
        }

        return $response->getContent();
    }

    protected function makeOrderHeaders(Order $order)
    {
        $orderId = $order->getId();

        return [
            'X-Order-Id' => $orderId,
            'X-Order-Link' => get_edit_post_link($orderId, null),
        ];
    }

    protected function getInstanceId($shippingAddress)
    {
        $fakePackage = [];
        $fakePackage['destination'] = [
            'country' => isset($shippingAddress['country']) ? $shippingAddress['country'] : null,
            'state' => isset($shippingAddress['state']) ? $shippingAddress['state'] : null,
            'postcode' => isset($shippingAddress['postcode']) ? $shippingAddress['postcode'] : null,
        ];

        $data_store = \WC_Data_Store::load( 'shipping-zone' );
        $zoneId = $data_store->get_zone_id_from_package($fakePackage);
        $shippingMethods = (new \WC_Shipping_Zone($zoneId))->get_shipping_methods();
        $filteredMethods = array_filter($shippingMethods, function($method) {
            return $method->id == I::FLAGSHIP_SHIPPING_PLUGIN_ID && $method->is_enabled();
        });
        
        if (count($filteredMethods) == 0) {
            return;
        }

        return reset($filteredMethods)->instance_id;
    }

    protected function getHSCodeFlag($order)
    {
        $hsCodeFlag = 1;
        $order_items = $order->native()->get_items();

        foreach ($order_items as $order_item) {
            $product = $order->native()->get_product_from_item($order_item);
            $hsCodeFlag = empty($product->get_attribute('hs-code')) ? 0 : $hsCodeFlag;
        }

        return $hsCodeFlag;
    }
}
