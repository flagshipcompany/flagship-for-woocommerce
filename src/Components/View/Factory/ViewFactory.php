<?php

namespace FS\Components\View\Factory;

class ViewFactory extends \FS\Components\AbstractComponent implements FactoryInterface, \FS\Components\Factory\DriverAwareInterface
{
    protected $driver;

    public function getView($resource, $context = array())
    {
        $view = $this->getFactoryDriver()->getView($resource, $context);

        if ($view) {
            return $view->setApplicationContext($this->getApplicationContext());
        }

        throw new \Exception('Unable to resolve view: '.$resource, 500);
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
