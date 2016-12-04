<?php

namespace FS\Configurations\WordPress\RequestBuilder\Factory;

class Driver extends \FS\Components\AbstractComponent implements \FS\Components\Shipping\RequestBuilder\Factory\FactoryInterface, \FS\Components\Factory\DriverInterface
{
    public function getBuilder($resource, $context = array())
    {
        switch ($resource) {
            case 'ShipperAddress':
                return new \FS\Configurations\WordPress\RequestBuilder\ShipperAddressBuilder();
                // no break
            case 'ReceiverAddress':
                if (isset($context['type']) && $context['type'] == 'cart') {
                    return new \FS\Configurations\WordPress\RequestBuilder\Cart\ReceiverAddressBuilder();
                } else {
                    return new \FS\Configurations\WordPress\RequestBuilder\Order\ReceiverAddressBuilder();
                }
                // no break
            case 'PackageBox':
                return new \FS\Configurations\WordPress\RequestBuilder\PackageBoxBuilder();
                // no break
            case 'ProductItem':
                return new \FS\Configurations\WordPress\RequestBuilder\ProductItemBuilder();
                // no break
            case 'PackageItems':
                $isCart = isset($context['type']) && $context['type'] == 'cart';
                $usePackingApi = isset($context['usePackingApi']) && $context['usePackingApi'];

                if ($isCart && $usePackingApi) {
                    return new \FS\Configurations\WordPress\RequestBuilder\Cart\PackageItems\ApiBuilder();
                }

                if ($isCart) {
                    return new \FS\Configurations\WordPress\RequestBuilder\Cart\PackageItems\FallbackBuilder();
                }

                if (!$isCart && $usePackingApi) {
                    return new \FS\Configurations\WordPress\RequestBuilder\Order\PackageItems\ApiBuilder();
                }

                return new \FS\Configurations\WordPress\RequestBuilder\Order\PackageItems\FallbackBuilder();

                // no break
            case 'ShippingService':
                return new \FS\Configurations\WordPress\RequestBuilder\Order\ShippingServiceBuilder();
                // no break
            case 'ShippingOptions':
                return new \FS\Configurations\WordPress\RequestBuilder\Order\ShippingOptionsBuilder();
                // no break
        }
    }

    public function getShipperAddressBuilder($context = array())
    {
        return new \FS\Configurations\WordPress\RequestBuilder\ShipperAddressBuilder();
    }
}
