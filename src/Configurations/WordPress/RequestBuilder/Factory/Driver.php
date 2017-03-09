<?php

namespace FS\Configurations\WordPress\RequestBuilder\Factory;

use FS\Components\AbstractComponent;
use FS\Components\Shipping\RequestBuilder\Factory\FactoryInterface;
use FS\Components\Factory\DriverInterface;

class Driver extends AbstractComponent implements FactoryInterface, DriverInterface
{
    public function getBuilder($resource, $context = array())
    {
        switch ($resource) {
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
            case 'ShippingOptions':
                return new \FS\Configurations\WordPress\RequestBuilder\Order\ShippingOptionsBuilder();
                // no break
            case 'CommercialInvoice':
                return new \FS\Configurations\WordPress\RequestBuilder\Order\CommercialInvoiceBuilder();
        }
    }

    public function getShipperAddressBuilder($context = array())
    {
        return new \FS\Configurations\WordPress\RequestBuilder\ShipperAddressBuilder();
    }

    public function getShippingServiceBuilder($context = array())
    {
        return new \FS\Configurations\WordPress\RequestBuilder\Order\ShippingServiceBuilder();
    }
}
