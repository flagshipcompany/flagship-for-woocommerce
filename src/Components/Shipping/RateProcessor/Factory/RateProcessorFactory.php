<?php

namespace FS\Components\Shipping\RateProcessor\Factory;

class RateProcessorFactory extends \FS\Components\AbstractComponent implements FactoryInterface, \FS\Components\Factory\DriverAwareInterface
{
    protected $driver;

    public function getRateProcessor($resource, $context = array())
    {
        return $this->getFactoryDriver()->getRateProcessor($resource, $context);
    }

    public function setFactoryDriver(\FS\Components\Factory\DriverInterface $driver)
    {
        $this->driver = $driver;

        return $this;
    }

    public function getFactoryDriver()
    {
        return $this->driver;
    }
}
