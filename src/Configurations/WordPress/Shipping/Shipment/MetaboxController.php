<?php

namespace FS\Configurations\WordPress\Shipping\Shipment;

class MetaboxController extends \FS\Components\AbstractComponent
{
    public function display(\FS\Components\Shop\OrderInterface $order)
    {
        $notifier = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Notifier');
        $view = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\View\\Factory\\ViewFactory')
            ->getView(\FS\Configurations\WordPress\View\Factory\Driver::RESOURCE_METABOX);
        $settings = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Settings');

        $shipment = $order->getShipment();
        $service = $order->getShippingService();

        $notifier->view();

        // shipment created
        if ($shipment) {
            return $view->render(array(
                'type' => 'created',
                'shipment' => $shipment,
            ));
        }

        $payload = array();

        // quoted but no shipment created
        if (!$shipment && $order->hasQuote()) {
            $payload['type'] = 'create';
            $payload['service'] = $service;
            $payload['cod'] = array(
                'currency' => strtoupper(\get_woocommerce_currency()),
            );
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

    public function createShipment(\FS\Components\Shop\OrderInterface $order)
    {
        $options = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Options');
        $notifier = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Notifier');

        $shipment = $order->getShipment();

        if ($shipment) {
            $notifier->warning(sprintf('You have flagship shipment for this order. FlagShip ID (%s)', $this->shipment['shipment_id']));

            return $this;
        }

        $client = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Http\\Client');
        $command = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Shipping\\Command');
        $factory = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Shipping\\Factory\\ShoppingOrderConfirmationRequestFactory');

        $response = $command->confirm(
            $client,
            $factory->setPayload(array(
                'order' => $order,
                'request' => $this->getApplicationContext()->getComponent('\\FS\\Components\\Web\\RequestParam'),
                'options' => $options,
            ))->getRequest()
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

    public function voidShipment(\FS\Components\Shop\OrderInterface $order)
    {
        $options = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Options');
        $notifier = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Notifier');

        $shipment = $order->getShipment();

        if (!$shipment) {
            $notifier->warning(sprintf('Unable to access shipment with FlagShip ID (%s)', $shipment->getId()));

            return;
        }

        $client = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Http\\Client');

        $response = $client->delete('/ship/shipments/'.$shipment->getId());

        if (!$response->isSuccessful()) {
            $notifier->warning(sprintf('Unable to void shipment with FlagShip ID (%s)', $shipment->getId()));

            return;
        }

        if (empty($shipment['pickup'])) {
            unset($order['flagship_shipping_raw']);

            return;
        }

        $this->voidPickup($order);

        unset($order['flagship_shipping_raw']);
    }

    public function requoteShipment(\FS\Components\Shop\OrderInterface $order)
    {
        $options = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Options');
        $settings = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Settings');
        $client = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Http\\Client');
        $command = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Shipping\\Command');
        $factory = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Shipping\\Factory\\ShoppingOrderRateRequestFactory');
        $notifier = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Notifier');
        $rateProcessorFactory = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Shipping\\RateProcessor\\Factory\\RateProcessorFactory');

        $response = $command->quote(
            $client,
            $factory->setPayload(array(
                'order' => $order,
                'options' => $options,
            ))->getRequest()
        );

        if (!$response->isSuccessful()) {
            $notifier->error('Flagship Shipping has some difficulty in retrieving the rates. Please contact site administrator for assistance.<br/>');

            return;
        }

        $service = $order->getShippingService();

        $rates = $response->getContent();

        $rates = $rateProcessorFactory
            ->getRateProcessor('ProcessRate')
            ->getProcessedRates($rates, array(
                'factory' => $rateProcessorFactory,
                'options' => $options,
                'instanceId' => $service['instance_id'] ? $service['instance_id'] : false,
                'methodId' => $settings['FLAGSHIP_SHIPPING_PLUGIN_ID'],
            ));

        $wcShippingRates = array();

        foreach ($rates as $rate) {
            $wcShippingRates[$rate['id']] = $rate['label'].' $'.$rate['cost'];
        }

        if ($wcShippingRates) {
            $order['flagship_shipping_requote_rates'] = $wcShippingRates;
        }
    }

    public function schedulePickup(\FS\Components\Shop\OrderInterface $order)
    {
        $options = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Options');
        $notifier = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Notifier');
        $client = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Http\\Client');
        $command = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Shipping\\Command');
        $request = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Web\\RequestParam');
        $factory = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Shipping\\Factory\\ShoppingOrderPickupRequestFactory');

        $shipment = $order->getShipment();

        if (!$shipment) {
            return;
        }

        $response = $command->pickup(
            $client,
            $factory->setPayload(array(
                'order' => $order,
                'options' => $options,
                'shipment' => $shipment,
                'date' => $request->request->get('flagship_shipping_pickup_schedule_date', date('Y-m-d')),
            ))->getRequest()
        );

        if (!$response->isSuccessful()) {
            $notifier->warning(sprintf('Unable to schedule pick-up with FlagShip ID (%s)', $shipment['shipment_id']));

            return;
        }

        $shipment['pickup'] = $response->getContent();

        $order['flagship_shipping_raw'] = $shipment->jsonSerialize();
    }

    public function voidPickup(\FS\Components\Shop\OrderInterface $order)
    {
        $options = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Options');
        $client = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Http\\Client');
        $notifier = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Notifier');

        $shipment = $order->getShipment();

        $response = $client->delete('/pickups/'.$shipment['pickup']['id']);

        if (!$response->isSuccessful()) {
            $notifier->warning(sprintf('Unable to void pick-up with FlagShip Pickup ID (%s)', $shipment['pickup']['id']));

            return;
        }

        unset($shipment['pickup']);

        $order['flagship_shipping_raw'] = $shipment->jsonSerialize();
    }
}
