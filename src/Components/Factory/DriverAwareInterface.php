<?php

namespace FS\Components\Factory;

interface DriverAwareInterface
{
    public function setFactoryDriver(DriverInterface $driver);

    public function getFactoryDriver();
}
