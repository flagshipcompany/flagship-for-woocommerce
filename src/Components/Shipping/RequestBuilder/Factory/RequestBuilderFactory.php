<?php

namespace FS\Components\Shipping\RequestBuilder\Factory;

use FS\Components\AbstractComponent;
use FS\Components\Shipping\RequestBuilder;

class RequestBuilderFactory extends AbstractComponent implements FactoryInterface
{
    public function getBuilder($resource, $context = array())
    {
        switch ($resource) {
            case 'ReceiverAddress':
                if (isset($context['type']) && $context['type'] == 'cart') {
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
                $isCart = isset($context['type']) && $context['type'] == 'cart';
                $usePackingApi = isset($context['usePackingApi']) && $context['usePackingApi'];

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
        }
    }

    public function getShipperAddressBuilder($context = array())
    {
        return new RequestBuilder\ShipperAddressBuilder();
    }

    public function getShippingServiceBuilder($context = array())
    {
        return new RequestBuilder\Order\ShippingServiceBuilder();
    }
}
