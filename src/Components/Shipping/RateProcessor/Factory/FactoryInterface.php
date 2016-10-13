<?php

namespace FS\Components\Shipping\RateProcessor\Factory;

interface FactoryInterface
{
    public function getRateProcessor($resource, $context = array());
}
