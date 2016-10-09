<?php

namespace FS\Configurations\WordPress\Event;

class ApplicationListenerFactory extends \FS\Components\AbstractComponent
{
    public function addApplicationListeners(\FS\Context\ConfigurableApplicationContextInterface $context)
    {
        $context
            ->addApplicationListener($this->getNativeHookPublishedListener(new Listener\PluginInitialization(), $context))
            ->addApplicationListener($this->getNativeHookPublishedListener(new Listener\MetaboxOperations(), $context))
            ->addApplicationListener($this->getNativeHookPublishedListener(new Listener\MetaboxDisplay(), $context))
            ->addApplicationListener($this->getNativeHookPublishedListener(new Listener\ShippingMethodSetup(), $context))
            ->addApplicationListener($this->getNativeHookPublishedListener(new Listener\ShippingZoneMethodOptions(), $context));

        if (\is_admin()) {
            $context->addApplicationListener($this->getNativeHookPublishedListener(new Listener\PluginPageSettingLink(), $context));
            $context->addApplicationListener($this->getNativeHookPublishedListener(new Listener\PickupPostType(), $context));
        }
    }

    protected function getNativeHookPublishedListener(\FS\Configurations\WordPress\Event\NativeHookInterface $listener, \FS\Context\ConfigurableApplicationContextInterface $context)
    {
        $listener->publishNativeHook($context);

        return $listener;
    }
}
