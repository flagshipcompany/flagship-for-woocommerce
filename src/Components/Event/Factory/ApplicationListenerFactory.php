<?php

namespace FS\Components\Event\Factory;

use FS\Components\AbstractComponent;
use FS\Configurations\WordPress\Event\Listener;

class ApplicationListenerFactory extends AbstractComponent
{
    public function addApplicationListeners(\FS\Context\ConfigurableApplicationContextInterface $context)
    {
        $context
            ->addApplicationListener($this->getNativeHookPublishedListener(new Listener\PluginInitialization(), $context))
            ->addApplicationListener($this->getNativeHookPublishedListener(new Listener\MetaboxOperations(), $context))
            ->addApplicationListener($this->getNativeHookPublishedListener(new Listener\MetaboxDisplay(), $context))
            ->addApplicationListener($this->getNativeHookPublishedListener(new Listener\ShippingMethodSetup(), $context))
            ->addApplicationListener($this->getNativeHookPublishedListener(new Listener\ShippingZoneMethodOptions(), $context))
            ->addApplicationListener(new Listener\CalculateShipping());

        if (\is_admin()) {
            $context->addApplicationListener($this->getNativeHookPublishedListener(new Listener\PluginPageSettingLink(), $context));
            $context->addApplicationListener($this->getNativeHookPublishedListener(new Listener\PickupPostType(), $context));
            $context->addApplicationListener($this->getNativeHookPublishedListener(new Listener\ShippingZoneMethodAdd(), $context));
        }
    }

    protected function getNativeHookPublishedListener(\FS\Configurations\WordPress\Event\NativeHookInterface $listener, \FS\Context\ConfigurableApplicationContextInterface $context)
    {
        $listener->publishNativeHook($context);

        return $listener;
    }
}
