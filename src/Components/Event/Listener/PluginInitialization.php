<?php

namespace FS\Components\Event\Listener;

use FS\Components\AbstractComponent;
use FS\Context\ApplicationListenerInterface;
use FS\Components\Event\NativeHookInterface;
use FS\Components\Event\ApplicationEvent;

class PluginInitialization extends AbstractComponent implements ApplicationListenerInterface, NativeHookInterface
{
    public function getSupportedEvent()
    {
        return ApplicationEvent::PLUGIN_INITIALIZATION;
    }

    public function onApplicationEvent(
        \FS\Context\ApplicationEventInterface $event,
        \FS\Context\ConfigurableApplicationContextInterface $context
    ) {
    }

    public function publishNativeHook(\FS\Context\ConfigurableApplicationContextInterface $context)
    {
        \add_action('init', function () {
            load_plugin_textdomain(FLAGSHIP_SHIPPING_TEXT_DOMAIN, false, 'flagship-for-woocommerce/languages/');
        });
    }

    public function getNativeHookType()
    {
        return self::TYPE_ACTION;
    }
}
