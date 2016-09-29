<?php

namespace FS\Components\Shipping\RequestBuilder\Drivers\WordPress;

class FactoryDriver extends \FS\Components\AbstractComponent implements \FS\Components\Shipping\RequestBuilder\Factory\FactoryDriverInterface
{
    public function getBuilder($resource, $context = array())
    {
        switch ($resource) {
            case 'ShipperAddress':
                return new ShipperAddressBuilder();
                // no break
            case 'ReceiverAddress':
                if (isset($context['type']) && $context['type'] == 'cart') {
                    return new Cart\ReceiverAddressBuilder();
                } else {
                    return new Order\ReceiverAddressBuilder();
                }
                // no break
            case 'PackageItems':
                if (isset($context['type']) && $context['type'] == 'cart') {
                    return new Cart\PackageItemsBuilder();
                } else {
                    return new Order\PackageItemsBuilder();
                }
                // no break
            case 'ShippingService':
                return new Order\ShippingServiceBuilder();
                // no break
            case 'ShippingOptions':
                return new Order\ShippingOptionsBuilder();
                // no break
        }
    }
}
