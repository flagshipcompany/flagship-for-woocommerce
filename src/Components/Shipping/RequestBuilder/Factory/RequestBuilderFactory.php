<?php

namespace FS\Components\Shipping\RequestBuilder\Factory;

use FS\Components\AbstractComponent;
use FS\Components\Shipping\RequestBuilder;
use FS\Context\Factory\FactoryInterface;

class RequestBuilderFactory extends AbstractComponent implements FactoryInterface
{
    public function resolve($resource, $option = [])
    {
        switch ($resource) {
            case 'ReceiverAddress':
                if (isset($option['type']) && $option['type'] == 'cart') {
                    return new RequestBuilder\Cart\ReceiverAddressBuilder();
                } else {
                    return new RequestBuilder\Order\ReceiverAddressBuilder();
                }
                // no break
            case 'PackageBox':
                return new RequestBuilder\PackageBoxBuilder();
                // no break
            case 'ProductItem':
                return new RequestBuilder\ProductItemBuilder();
                // no break
            case 'PackageItems':
                $isCart = isset($option['type']) && $option['type'] == 'cart';
                $usePackingApi = isset($option['usePackingApi']) && $option['usePackingApi'];

                if ($isCart && $usePackingApi) {
                    return new RequestBuilder\Cart\PackageItems\ApiBuilder();
                }

                if ($isCart) {
                    return new RequestBuilder\Cart\PackageItems\FallbackBuilder();
                }

                if (!$isCart && $usePackingApi) {
                    return new RequestBuilder\Order\PackageItems\ApiBuilder();
                }

                return new RequestBuilder\Order\PackageItems\FallbackBuilder();

                // no break
            case 'ShippingOptions':
                return new RequestBuilder\Order\ShippingOptionsBuilder();
                // no break
            case 'CommercialInvoice':
                return new RequestBuilder\Order\CommercialInvoiceBuilder();
            case 'ShipperAddress':
                return new RequestBuilder\ShipperAddressBuilder();
            case 'ShippingService':
                return new RequestBuilder\Order\ShippingServiceBuilder();
        }
    }
}
