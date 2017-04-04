<?php

namespace FS\Context;

use FS\Components\Factory\ComponentFactoryInterface;
use FS\Components\Factory\ConfigurationInterface;

abstract class AbstractApplicationContext implements
    ConfigurableApplicationContextInterface,
    ApplicationEventPublisherInterface,
    ComponentFactoryInterface
{
    protected $container;

    public function addApplicationListener(ApplicationListenerInterface $listener)
    {
        $this->getApplicationEventCaster()->addApplicationListener($listener);

        return $this;
    }

    public function getApplicationEventCaster()
    {
        return $this->_('\\FS\\Components\\Event\\ApplicationEventCaster');
    }

    public function publishEvent(ApplicationEventInterface $event)
    {
        return $this->getApplicationEventCaster()->castEvent($event);
    }

    public function setContainer(\FS\Container $container)
    {
        $this->container = $container;

        return $this;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function setConfiguration(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }

    public function configure(ConfigurationInterface $configurator)
    {
        $configurator->configure($this);
    }

    public function getComponent($class)
    {
        $factory = new \FS\Components\Factory\ComponentFactory();
        $factory->setApplicationContext($this);

        return $factory->getComponent($class);
    }

    /**
     * yes, this method name is merely a underscore.
     * alias of getComponent.
     *
     * @param string $class
     *
     * @return Component
     */
    public function _($class)
    {
        return $this->getComponent($class);
    }
}
