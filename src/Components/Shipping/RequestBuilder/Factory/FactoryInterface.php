<?php

namespace FS\Components\Shipping\RequestBuilder\Factory;

interface FactoryInterface
{
    public function getBuilder($resource, $context = array());
}
