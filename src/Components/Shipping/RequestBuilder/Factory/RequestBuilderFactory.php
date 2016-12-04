<?php

namespace FS\Components\Shipping\RequestBuilder\Factory;

class RequestBuilderFactory extends \FS\Components\AbstractComponent implements FactoryInterface, \FS\Components\Factory\DriverAwareInterface
{
    protected $driver;

    public function getBuilder($resource, $context = array())
    {
        return $this->getFactoryDriver()
            ->getBuilder($resource, $context)
            ->setApplicationContext($this->getApplicationContext());
    }

    public function getShipperAddressBuilder($context = array())
    {
        return $this->getFactoryDriver()
            ->getShipperAddressBuilder($context)
            ->setApplicationContext($this->getApplicationContext());
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
