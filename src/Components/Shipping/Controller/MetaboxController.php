<?php

namespace FS\Components\Shipping\Controller;

use FS\Components\AbstractComponent;
use FS\Components\Shipping\Object\Shipping;
use FS\Components\Web\RequestParam as Req;
use FS\Context\ApplicationContext as Context;

class MetaboxController extends AbstractComponent
{
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

        if ($shipment->isCreated()) {
            $context->alert()->warning('You have flagship shipment for this order. FlagShip ID (%s)', [$shipment->getId()]);

            return $this;
        }

        $order = $shipping->getOrder();

        $factory = $context
            ->_('\\FS\\Components\\Shipping\\Request\\Factory\\ShoppingOrderConfirmation');

        $response = $context->command()->confirm(
            $context->api(),
            $factory->setPayload([
                'shipping' => $shipping,
                'request' => $request,
                'options' => $context->option(),
            ])->getRequest()
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
        $factory = $context
            ->_('\\FS\\Components\\Shipping\\Request\\Factory\\ShoppingOrderRate');
        $rateProcessorFactory = $context
            ->_('\\FS\\Components\\Shipping\\RateProcessor\\Factory\\RateProcessorFactory');

        $response = $context->command()->quote(
            $context->api(),
            $factory->setPayload([
                'shipping' => $shipping,
                'options' => $context->option(),
            ])->getRequest()
        );

        if (!$response->isSuccessful()) {
            $context->alert()->error('Flagship Shipping has some difficulty in retrieving the rates. Please contact site administrator for assistance.<br/>');

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

    public function schedulePickup(Req $request, Context $context, Shipping $shipping)
    {
        $factory = $context
            ->_('\\FS\\Components\\Shipping\\Request\\Factory\\ShoppingOrderPickup');

        $shipment = $shipping->getShipment();

        if (!$shipment->isCreated()) {
            return;
        }

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

        $response = $context->api()->delete('/pickups/'.$pickup->getId());

        if (!$response->isSuccessful()) {
            $context->alert()->warning('Unable to void pick-up with FlagShip Pickup ID (%s)', [$pickup->getId()]);

            return;
        }

        $shipping->getShipment()->remove('pickup');

        $shipping->save();
    }
}
