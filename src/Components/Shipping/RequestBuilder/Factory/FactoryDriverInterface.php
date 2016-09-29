<?php

namespace FS\Components\Shipping\RequestBuilder\Factory;

interface FactoryDriverInterface extends FactoryInterface
{
    public function getBuilder($resource, $context = array());
}
