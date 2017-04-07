<?php

namespace FS\Components\Shipping\Controller;

use FS\Components\AbstractComponent;
use FS\Components\Shop\OrderInterface as Ord;
use FS\Components\Web\RequestParam as Req;
use FS\Context\ApplicationContext as App;

class MetaboxController extends AbstractComponent
{
    public function display(Req $request, App $context, Ord $order)
    {
        $view = $context
            ->_('\\FS\\Components\\View\\Factory\\ViewFactory')
            ->resolve(\FS\Components\View\Factory\ViewFactory::RESOURCE_METABOX);

        $shipment = $order->getShipment();
        $service = $order->getShippingService();

        // shipment created
        if ($shipment) {
            return $view->render([
                'type' => 'created',
                'shipment' => $shipment,
            ]);
        }

        $payload = [];

        // quoted but no shipment created
        if (!$shipment && $order->hasQuote()) {
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
        if ($requoteRates = $order['flagship_shipping_requote_rates']) {
            $payload['requote_rates'] = $requoteRates;
        }

        $view->render($payload);
    }

    public function createShipment(Req $request, App $context, Ord $order)
    {
        $shipment = $order->getShipment();

        if ($shipment) {
            $context->alert(sprintf('You have flagship shipment for this order. FlagShip ID (%s)', $shipment['shipment_id']), 'warning');

            return $this;
        }

        $command = $context
            ->_('\\FS\\Components\\Shipping\\Command');
        $factory = $context
            ->_('\\FS\\Components\\Shipping\\Request\\Factory\\ShoppingOrderConfirmation');

        $response = $command->confirm(
            $context->api(),
            $factory->setPayload([
                'order' => $order,
                'request' => $request,
                'options' => $context->option(),
            ])->getRequest()
        );

        if (!$response->isSuccessful()) {
            return;
        }

        $confirmed = $response->getContent();

        unset($order['flagship_shipping_requote_rates']);

        $order['flagship_shipping_shipment_id'] = $confirmed['shipment_id'];
        $order['flagship_shipping_shipment_tracking_number'] = $confirmed['tracking_number'];
        $order['flagship_shipping_courier_name'] = $confirmed['service']['courier_name'];
        $order['flagship_shipping_courier_service_code'] = $confirmed['service']['courier_code'];

        $order['flagship_shipping_raw'] = $confirmed;
    }

    public function voidShipment(Req $request, App $context, Ord $order)
    {
        $shipment = $order->getShipment();

        if (!$shipment) {
            $context->alert(sprintf('Unable to access shipment with FlagShip ID (%s)', $shipment->getId()), 'warning');

            return;
        }

        $response = $context->api()->delete('/ship/shipments/'.$shipment->getId());

        if (!$response->isSuccessful()) {
            $context->alert(sprintf('Unable to void shipment with FlagShip ID (%s)', $shipment->getId()), 'warning');

            return;
        }

        if (empty($shipment['pickup'])) {
            unset($order['flagship_shipping_raw']);

            return;
        }

        $this->voidPickup($order);

        unset($order['flagship_shipping_raw']);
    }

    public function requoteShipment(Req $request, App $context, Ord $order)
    {
        $command = $context
            ->_('\\FS\\Components\\Shipping\\Command');
        $factory = $context
            ->_('\\FS\\Components\\Shipping\\Request\\Factory\\ShoppingOrderRate');
        $rateProcessorFactory = $context
            ->_('\\FS\\Components\\Shipping\\RateProcessor\\Factory\\RateProcessorFactory');

        $response = $command->quote(
            $context->api(),
            $factory->setPayload([
                'order' => $order,
                'options' => $context->option(),
            ])->getRequest()
        );

        if (!$response->isSuccessful()) {
            $context->alert('Flagship Shipping has some difficulty in retrieving the rates. Please contact site administrator for assistance.<br/>', 'error');

            return;
        }

        $service = $order->getShippingService();

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
            $order['flagship_shipping_requote_rates'] = $wcShippingRates;
        }
    }

    public function schedulePickup(Req $request, App $context, Ord $order)
    {
        $command = $context
            ->_('\\FS\\Components\\Shipping\\Command');
        $factory = $context
            ->_('\\FS\\Components\\Shipping\\Request\\Factory\\ShoppingOrderPickup');

        $shipment = $order->getShipment();

        if (!$shipment) {
            return;
        }

        $response = $command->pickup(
            $context->api(),
            $factory->setPayload([
                'order' => $order,
                'options' => $context->option(),
                'shipment' => $shipment,
                'date' => $request->request->get('flagship_shipping_pickup_schedule_date', date('Y-m-d')),
            ])->getRequest()
        );

        if (!$response->isSuccessful()) {
            $context->alert(sprintf('Unable to schedule pick-up with FlagShip ID (%s)', $shipment['shipment_id']), 'warning');

            return;
        }

        $shipment['pickup'] = $response->getContent();

        $order['flagship_shipping_pickup'] = $shipment['pickup'];
        $order['flagship_shipping_raw'] = (array) $shipment;
    }

    public function voidPickup(Req $request, App $context, Ord $order)
    {
        $shipment = $order->getShipment();

        $response = $context->api()->delete('/pickups/'.$shipment['pickup']['id']);

        if (!$response->isSuccessful()) {
            $context->alert(sprintf('Unable to void pick-up with FlagShip Pickup ID (%s)', $shipment['pickup']['id']), 'warning');

            return;
        }

        unset($shipment['pickup']);

        $order['flagship_shipping_raw'] = $shipment->jsonSerialize();
    }
}
