<?php

namespace FS\Configurations\WordPress\RequestBuilder\Order;

use FS\Components\AbstractComponent;
use FS\Components\Shipping\RequestBuilder\RequestBuilderInterface;

class ShippingServiceBuilder extends AbstractComponent implements RequestBuilderInterface
{
    public function build($payload = null)
    {
        $overloadShippingMethod = $payload['request']->request->get('flagship_shipping_service');

        $service = $payload['order']->getShippingService($overloadShippingMethod);

        $retService = array();

        foreach ($service as $key => $value) {
            if ($key == 'courier_name' || $key == 'courier_code') {
                $retService[$key] = $value;
            }
        }

        return $retService;
    }
}
