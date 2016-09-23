<?php

namespace FS\Components\Order;

class MetaBox extends \FS\Components\AbstractComponent
{
    public function display(\FS\Components\Order\ShoppingOrder $order)
    {
        $notifier = $this->ctx->getComponent('\\FS\\Components\\Notifier');
        $notifier->scope('shop_order', array('id' => $order->getId()));

        $factory = $this->ctx->getComponent('\\FS\\Components\\Order\\Factory\\MetaBoxViewerFactory');
        $viewer = $factory->getViewer($order);

        $notifier->view();
        $viewer->render();
    }

    public function createShipment(\FS\Components\Order\ShoppingOrder $order)
    {
        $service = $order->getShippingService();
        $shippingMethodInstanceId = $service['instance_id'] ? $service['instance_id'] : false;

        $options = $this->ctx->getComponent('\\FS\\Components\\Options');
        $options->sync($shippingMethodInstanceId);

        $notifier = $this->ctx->getComponent('\\FS\\Components\\Notifier');
        $notifier->scope('shop_order', array('id' => $order->getId()));

        $client = $this->ctx->getComponent('\\FS\\Components\\Http\\Client');
        $client->setToken($options->get('token'));

        $command = $this->ctx->getComponent('\\FS\\Components\\Shipping\\Command');

        $shipment = $order->get('flagship_shipping_raw');

        if ($shipment) {
            $notifier->warning(sprintf('You have flagship shipment for this order. FlagShip ID (%s)', $this->shipment['shipment_id']));

            return $this;
        }

        $factory = $this->ctx->getComponent('\\FS\\Components\\Shipping\\Factory\\ShoppingOrderConfirmationRequestFactory');

        $response = $command->confirm(
            $client,
            $factory->setPayload(array(
                'order' => $order,
                'request' => $this->ctx->getComponent('\\FS\\Components\\Web\\RequestParam'),
                'options' => $options,
            ))->getRequest()
        );

        if (!$response->isSuccessful()) {
            return;
        }

        $confirmed = $response->getBody();

        $order->delete('flagship_shipping_requote_rates')
            ->set('flagship_shipping_shipment_id', $confirmed['shipment_id'])
            ->set('flagship_shipping_shipment_tracking_number', $confirmed['tracking_number'])
            ->set('flagship_shipping_courier_name', $confirmed['service']['courier_name'])
            ->set('flagship_shipping_courier_service_code', $confirmed['service']['courier_code'])
            ->set('flagship_shipping_raw', $confirmed);
    }

    public function voidShipment(\FS\Components\Order\ShoppingOrder $order)
    {
        $service = $order->getShippingService();
        $shippingMethodInstanceId = $service['instance_id'] ? $service['instance_id'] : false;

        $options = $this->ctx->getComponent('\\FS\\Components\\Options');
        $options->sync($shippingMethodInstanceId);

        $notifier = $this->ctx->getComponent('\\FS\\Components\\Notifier');
        $notifier->scope('shop_order', array('id' => $order->getId()));

        $shipment = $order->get('flagship_shipping_raw');
        $shipmentId = $order->get('flagship_shipping_shipment_id');

        if (!$shipment || !$shipmentId) {
            $notifier->warning(sprintf('Unable to access shipment with FlagShip ID (%s)', $shipmentId));

            return;
        }

        $client = $this->ctx->getComponent('\\FS\\Components\\Http\\Client');
        $client->setToken($options->get('token'));

        $response = $client->delete('/ship/shipments/'.$shipmentId);

        if (!$response->isSuccessful()) {
            $notifier->warning(sprintf('Unable to void shipment with FlagShip ID (%s)', $shipmentId));

            return;
        }

        if (empty($shipment['pickup'])) {
            $order->delete('flagship_shipping_raw');

            return;
        }

        $this->voidPickup($order);

        $order->delete('flagship_shipping_raw');
    }

    public function requoteShipment(\FS\Components\Order\ShoppingOrder $order)
    {
        $service = $order->getShippingService();
        $shippingMethodInstanceId = $service['instance_id'] ? $service['instance_id'] : false;

        $options = $this->ctx->getComponent('\\FS\\Components\\Options');
        $options->sync($shippingMethodInstanceId);

        $client = $this->ctx->getComponent('\\FS\\Components\\Http\\Client');
        $client->setToken($options->get('token'));

        $command = $this->ctx->getComponent('\\FS\\Components\\Shipping\\Command');
        $factory = $this->ctx->getComponent('\\FS\\Components\\Shipping\\Factory\\ShoppingOrderRateRequestFactory');
        $rateProcessor = $this->ctx->getComponent('\\FS\\Components\\Shipping\\RateProcessor');
        $notifier = $this->ctx->getComponent('\\FS\\Components\\Notifier');
        $notifier->scope('shop_order', array('id' => $order->getId()));

        $response = $command->quote(
            $client,
            $factory->setPayload(array(
                'order' => $order->getWcOrder(),
                'options' => $options,
            ))->getRequest()
        );

        if (!$response->isSuccessful()) {
            $notifier->error('Flagship Shipping has some difficulty in retrieving the rates. Please contact site administrator for assistance.<br/>');

            return;
        }

        $rates = $rateProcessor->convertToWcShippingRate($response->getBody(), $shippingMethodInstanceId);

        $wcShippingRates = array();

        foreach ($rates as $rate) {
            $wcShippingRates[$rate['id']] = $rate['label'].' $'.$rate['cost'];
        }

        if ($wcShippingRates) {
            $order->set('flagship_shipping_requote_rates', $wcShippingRates);
        }
    }

    public function schedulePickup(\FS\Components\Order\ShoppingOrder $order)
    {
        $service = $order->getShippingService();
        $shippingMethodInstanceId = $service['instance_id'] ? $service['instance_id'] : false;

        $options = $this->ctx->getComponent('\\FS\\Components\\Options');
        $options->sync($shippingMethodInstanceId);

        $notifier = $this->ctx->getComponent('\\FS\\Components\\Notifier');
        $notifier->scope('shop_order', array('id' => $order->getId()));

        $client = $this->ctx->getComponent('\\FS\\Components\\Http\\Client');
        $client->setToken($options->get('token'));

        $command = $this->ctx->getComponent('\\FS\\Components\\Shipping\\Command');

        $request = $this->ctx->getComponent('\\FS\\Components\\Web\\RequestParam');

        $factory = $this->ctx->getComponent('\\FS\\Components\\Shipping\\Factory\\ShoppingOrderPickupRequestFactory');

        $shipment = $order->get('flagship_shipping_raw');

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

        $shipment['pickup'] = $response->getBody();

        $order->set('flagship_shipping_raw', $shipment);
    }

    public function voidPickup(\FS\Components\Order\ShoppingOrder $order)
    {
        $service = $order->getShippingService();
        $shippingMethodInstanceId = $service['instance_id'] ? $service['instance_id'] : false;

        $options = $this->ctx->getComponent('\\FS\\Components\\Options');
        $options->sync($shippingMethodInstanceId);

        $client = $this->ctx->getComponent('\\FS\\Components\\Http\\Client');
        $client->setToken($options->get('token'));

        $notifier = $this->ctx->getComponent('\\FS\\Components\\Notifier');
        $notifier->scope('shop_order', array('id' => $order->getId()));

        $shipment = $order->get('flagship_shipping_raw');

        $response = $client->delete('/pickups/'.$shipment['pickup']['id']);

        if (!$response->isSuccessful()) {
            $notifier->warning(sprintf('Unable to void pick-up with FlagShip Pickup ID (%s)', $shipment['pickup']['id']));

            return;
        }

        unset($shipment['pickup']);

        $order->set('flagship_shipping_raw', $shipment);
    }
}
