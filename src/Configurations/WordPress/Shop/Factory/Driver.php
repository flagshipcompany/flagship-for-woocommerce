<?php

namespace FS\Configurations\WordPress\Shop\Factory;

class Driver extends \FS\Components\AbstractComponent implements \FS\Components\Shop\Factory\FactoryInterface, \FS\Components\Factory\DriverInterface
{
    public function getOrder($resource, $context = array())
    {
        switch ($resource) {
            case self::RESOURCE_ORDER:
                $order = new \FS\Configurations\WordPress\Shop\Order();

                if ($wcOrder = \wc_get_order($context['id'])) {
                    return $order->setNativeOrder($wcOrder);
                }

                throw new \Exception('Unable to retieve WooCommerce Order with ID: '.$context['id']);
                // no break
            case self::RESOURCE_SHIPMENT:
                return new \FS\Configurations\WordPress\Shop\PhoneValidator();
                // no break
        }
    }
}
