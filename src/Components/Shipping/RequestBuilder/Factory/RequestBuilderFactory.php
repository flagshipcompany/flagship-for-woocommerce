<?php

namespace FS\Components\Shipping\RequestBuilder\Factory;

class RequestBuilderFactory extends \FS\Components\AbstractComponent implements FactoryInterface, \FS\Components\Factory\DriverAwareInterface
{
    protected $driver;

    public function getBuilder($resource, $context = array())
    {
        return $this->getFactoryDriver()->getBuilder($resource, $context);
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
