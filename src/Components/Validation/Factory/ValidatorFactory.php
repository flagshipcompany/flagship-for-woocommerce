<?php

namespace FS\Components\Validation\Factory;

class ValidatorFactory extends \FS\Components\AbstractComponent implements FactoryInterface, \FS\Components\Factory\DriverAwareInterface
{
    protected $driver;

    public function getValidator($resource, $context = array())
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
