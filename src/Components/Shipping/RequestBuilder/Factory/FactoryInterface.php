<?php

namespace FS\Components\Shipping\RequestBuilder\Factory;

interface FactoryInterface
{
    public function getBuilder($resource, $context = array());

    public function getShipperAddressBuilder($context = array());

    public function getShippingServiceBuilder($context = array());
}
