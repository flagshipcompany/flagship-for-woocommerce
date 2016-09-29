<?php

namespace FS\Components\Shipping\RequestBuilder\Factory;

class RequestBuilderFactory extends \FS\Components\AbstractComponent implements FactoryInterface, FactoryDriverAwareInterface
{
    protected $driver;

    public function getBuilder($resource, $context = array())
    {
        return $this->getFactoryDriver()->getBuilder($resource, $context);
    }

    public function setFactoryDriver(FactoryDriverInterface $driver)
    {
        $this->driver = $driver;

        return $this;
    }

    public function getFactoryDriver()
    {
        return $this->driver;
    }
}
