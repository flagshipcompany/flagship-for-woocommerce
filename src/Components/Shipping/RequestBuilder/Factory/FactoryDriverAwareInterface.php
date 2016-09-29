<?php

namespace FS\Components\Shipping\RequestBuilder\Factory;

interface FactoryDriverAwareInterface
{
    public function setFactoryDriver(FactoryDriverInterface $driver);

    public function getFactoryDriver();
}
