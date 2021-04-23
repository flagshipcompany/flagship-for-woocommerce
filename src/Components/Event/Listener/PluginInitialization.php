<?php

namespace FS\Components\Event\Listener;

use FS\Components\AbstractComponent;
use FS\Context\ApplicationListenerInterface;
use FS\Components\Event\NativeHookInterface;
use FS\Components\Event\ApplicationEvent;
use FS\Context\ApplicationEventInterface as Event;
use FS\Context\ConfigurableApplicationContextInterface as Context;

class PluginInitialization extends AbstractComponent implements ApplicationListenerInterface, NativeHookInterface
{
    public function getSupportedEvent()
    {
        return ApplicationEvent::PLUGIN_INITIALIZATION;
    }

    public function onApplicationEvent(Event $event, Context $context)
    {
    }

    public function publishNativeHook(Context $context)
    {
        \add_action('init', function () {
            load_plugin_textdomain(FLAGSHIP_SHIPPING_TEXT_DOMAIN, false, 'flagship-woocommerce-shipping/languages/');
        });
    }

    public function getNativeHookType()
    {
        return self::TYPE_ACTION;
    }
}
