<?php

namespace FS\Components\Factory;

class ComponentFactory implements ComponentFactoryInterface, \FS\Context\ApplicationContextAwareInterface
{
    protected $ctx;

    public function getComponent($class)
    {
        $scope = $class::getScope();
        $object;

        if ($scope == 'prototype') {
            return $this->createComponent($class);
        }

        $container = $this->ctx->getContainer();

        if (isset($container[$class])) {
            return $container[$class];
        }

        $container[$class] = $this->createComponent($class);

        return $container[$class];
    }

    public function setApplicationContext(\FS\Context\ApplicationContextInterface $ctx = null)
    {
        $this->ctx = $ctx;

        return $this;
    }

    protected function createComponent($class)
    {
        $configuration = $this->ctx->getConfiguration();

        $object = $this->createObject($configuration, $class);

        $reflected = new \ReflectionObject($object);

        if ($reflected->implementsInterface('\\FS\\Components\\Factory\\ComponentPostConstructInterface')) {
            $object->postConstruct();
        }

        if ($reflected->implementsInterface('\\FS\\Context\\ApplicationContextAwareInterface')) {
            $object->setApplicationContext($this->ctx);
        }

        if ($reflected->implementsInterface('\\FS\\Components\\Factory\\ComponentFactoryAwareInterface')) {
            $object->setComponentFactory($this);
        }

        if ($reflected->implementsInterface('\\FS\\Components\\Factory\\ComponentInitializingInterface')) {
            $object->afterPropertiesSet();
        }

        return $object;
    }

    protected function createObject(\FS\Components\Factory\ConfigurationInterface $configuration, $class)
    {
        $reflected = new \ReflectionObject($configuration);

        $targets = explode('\\', $class);
        $configurationMethod = 'get'.end($targets);

        if ($reflected->hasMethod($configurationMethod)) {
            return $configuration->{$configurationMethod}();
        }

        return new $class();
    }
}
