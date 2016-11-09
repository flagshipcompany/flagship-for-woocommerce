<?php

namespace FS\Components\Shop\Factory;

class ShopFactory extends \FS\Components\AbstractComponent implements FactoryInterface, \FS\Components\Factory\DriverAwareInterface
{
    protected $driver;

    public function getModel($resource, $context = array())
    {
        $model = $this->getFactoryDriver()->getModel($resource, $context);

        if ($model && is_array($model)) {
            foreach ($model as $m) {
                $m->setApplicationContext($this->getApplicationContext());
            }

            return $model;
        }

        if ($model) {
            return $model->setApplicationContext($this->getApplicationContext());
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
