<?php

namespace FS\Components\Order\Factory;

class MetaBoxViewerFactory extends \FS\Components\AbstractComponent implements ViewerFactoryInterface
{
    public function getViewer(\FS\Components\Order\ShoppingOrder $order)
    {
        $viewer = $this->getApplicationContext()->getComponent('\\FS\\Components\\Order\\Factory\\MetaBoxViewer');

        $viewer->setTemplate('meta-boxes/order-flagship-shipping-actions');
        $viewer->setPayload($this->makePayload($order));

        return $viewer;
    }

    public function makePayload(\FS\Components\Order\ShoppingOrder $order)
    {
        $payload = array();

        $settings = $this->getApplicationContext()->getComponent('\\FS\\Components\\Settings');
        $shipment = $order->get('flagship_shipping_raw');

        $service = $order->getShippingService();

        // shipment created
        if ($shipment) {
            $payload['type'] = 'created';
            $payload['shipment'] = $shipment;

            return $payload;
        }

        // quoted but no shipment created
        if (!$shipment && ($service['provider'] == $settings['FLAGSHIP_SHIPPING_PLUGIN_ID'])) {
            $payload['type'] = 'create';
            $payload['service'] = $service;
            $payload['cod'] = array(
                'currency' => strtoupper(get_woocommerce_currency()),
            );
        }

        // possibly not quoted with FS
        if (!isset($payload['type'])) {
            $payload['type'] = 'unavailable';
        }

        // requotes
        if ($requoteRates = $order->get('flagship_shipping_requote_rates')) {
            $payload['requote_rates'] = $requoteRates;
        }

        return $payload;
    }
}
