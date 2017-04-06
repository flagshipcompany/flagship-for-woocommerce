<?php

namespace FS\Components\Shipping\Request\Builder\Factory;

use FS\Context\Factory\AbstractFactory;
use FS\Components\Shipping\Request\Builder;

class RequestBuilderFactory extends AbstractFactory
{
    public function resolveWithoutContext($resource, array $option = [])
    {
        switch ($resource) {
            case 'ReceiverAddress':
                if (isset($option['type']) && $option['type'] == 'cart') {
                    return new Builder\Cart\ReceiverAddressBuilder();
                } else {
                    return new Builder\Order\ReceiverAddressBuilder();
                }
                // no break
            case 'PackageBox':
                return new Builder\PackageBoxBuilder();
                // no break
            case 'ProductItem':
                return new Builder\ProductItemBuilder();
                // no break
            case 'PackageItems':
                $isCart = isset($option['type']) && $option['type'] == 'cart';
                $usePackingApi = isset($option['usePackingApi']) && $option['usePackingApi'];

                if ($isCart && $usePackingApi) {
                    return new Builder\Cart\PackageItems\ApiBuilder();
                }

                if ($isCart) {
                    return new Builder\Cart\PackageItems\FallbackBuilder();
                }

                if (!$isCart && $usePackingApi) {
                    return new Builder\Order\PackageItems\ApiBuilder();
                }

                return new Builder\Order\PackageItems\FallbackBuilder();

                // no break
            case 'ShippingOptions':
                return new Builder\Order\ShippingOptionsBuilder();
                // no break
            case 'CommercialInvoice':
                return new Builder\Order\CommercialInvoiceBuilder();
            case 'ShipperAddress':
                return new Builder\ShipperAddressBuilder();
            case 'ShippingService':
                return new Builder\Order\ShippingServiceBuilder();
        }
    }
}
