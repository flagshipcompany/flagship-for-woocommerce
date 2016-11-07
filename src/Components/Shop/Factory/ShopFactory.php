<?php

namespace FS\Components\Shop\Factory;

class ShopFactory extends \FS\Components\AbstractComponent implements FactoryInterface, \FS\Components\Factory\DriverAwareInterface
{
    protected $driver;

    public function getOrder($resource, $context = array())
    {
        $order = $this->getFactoryDriver()->getOrder($resource, $context);

        if ($order) {
            return $order;
        }

        throw new \Exception('Unable to resolve shop order: '.$resource, 500);
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
