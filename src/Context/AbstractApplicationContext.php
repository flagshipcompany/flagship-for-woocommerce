<?php

namespace FS\Context;

abstract class AbstractApplicationContext implements ConfigurableApplicationContextInterface,
    ApplicationEventPublisherInterface,
    \FS\Components\Factory\ComponentFactoryInterface
{
    protected $container;

    public function addApplicationListener(ApplicationListenerInterface $listener)
    {
        $this->getApplicationEventCaster()->addApplicationListener($listener);

        return $this;
    }

    public function getApplicationEventCaster()
    {
        return $this->getComponent('\\FS\\Components\\Event\\ApplicationEventCaster');
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

    public function setConfiguration(\FS\Components\Factory\ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }

    public function getComponent($class)
    {
        $factory = new \FS\Components\Factory\ComponentFactory();
        $factory->setApplicationContext($this);

        return $factory->getComponent($class);
    }
}
