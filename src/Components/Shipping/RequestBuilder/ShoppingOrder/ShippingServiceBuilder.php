<?php

namespace FS\Components\Shipping\RequestBuilder\ShoppingOrder;

class ShippingServiceBuilder extends \FS\Components\AbstractComponent implements \FS\Components\Shipping\RequestBuilder\RequestBuilderInterface
{
    public function build($payload = null)
    {
        $overloadShippingMethod = $payload['request']->request->get('flagship_shipping_service');

        $service = $payload['order']->getShippingService($overloadShippingMethod);

        return array_filter($service, function ($key) {
            return $key == 'courier_name' || $key == 'courier_code';
        }, ARRAY_FILTER_USE_KEY);
    }
}
