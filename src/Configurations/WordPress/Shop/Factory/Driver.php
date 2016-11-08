<?php

namespace FS\Configurations\WordPress\Shop\Factory;

class Driver extends \FS\Components\AbstractComponent implements \FS\Components\Shop\Factory\FactoryInterface, \FS\Components\Factory\DriverInterface
{
    public function getModel($resource, $context = array())
    {
        switch ($resource) {
            case self::RESOURCE_ORDER:
                $order = new \FS\Configurations\WordPress\Shop\Order();

                if (isset($context['nativeOrder']) && $context['nativeOrder'] instanceof \WC_Order) {
                    return $order->setNativeOrder($context['nativeOrder']);
                }

                if (isset($context['id']) && $wcOrder = \wc_get_order($context['id'])) {
                    return $order->setNativeOrder($wcOrder);
                }

                throw new \Exception('Unable to retieve WooCommerce Order');
                // no break
            case self::RESOURCE_ORDER_COLLECTION:
                $orders = array();

                if (isset($context['ids'])) {
                    foreach ($context['id'] as $id) {
                        $orders[] = $this->getModel(self::RESOURCE_ORDER, array(
                            'id' => $id,
                        ));
                    }

                    return $orders;
                }

                throw new \Exception('Unable to retieve WooCommerce Orders');
                // no break
            case self::RESOURCE_SHIPMENT:
                if ($context['raw']) {
                    $shipment = new \FS\Configurations\WordPress\Shop\Shipment();

                    foreach ($context['raw'] as $key => $value) {
                        $shipment[$key] = $value;
                    }

                    return $shipment;
                }

                throw new \Exception('Unable to retieve FlagShip shipment data');
                // no break
        }
    }
}
