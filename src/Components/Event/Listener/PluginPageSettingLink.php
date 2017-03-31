<?php

namespace FS\Components\Event\Listener;

use FS\Injection\I;
use FS\Components\AbstractComponent;
use FS\Context\ApplicationListenerInterface;
use FS\Components\Event\NativeHookInterface;
use FS\Context\ConfigurableApplicationContextInterface as Context;
use FS\Context\ApplicationEventInterface as Event;
use FS\Components\Event\ApplicationEvent;

class PluginPageSettingLink extends AbstractComponent implements ApplicationListenerInterface, NativeHookInterface
{
    public function getSupportedEvent()
    {
        return ApplicationEvent::PLUGIN_PAGE_SETTING_LINK;
    }

    public function onApplicationEvent(Event $event, Context $context)
    {
        $links = $event->getInput('links');

        if ($event->getInput('file') == I::basename()) {
            array_unshift($links, $context->_('\\FS\\Components\\Html')->a('flagship_shipping_settings', __('Settings', FLAGSHIP_SHIPPING_TEXT_DOMAIN), array(
                'escape' => true,
                'target' => true,
            )));
        }

        return $links;
    }

    public function publishNativeHook(Context $context)
    {
        \add_filter('plugin_action_links_'.I::basename(), function ($links, $file) use ($context) {
            $event = new ApplicationEvent(ApplicationEvent::PLUGIN_PAGE_SETTING_LINK);
            $event->setInputs(array(
                'links' => $links,
                'file' => $file,
            ));

            return $context->publishEvent($event);
        }, 10, 2);
    }

    public function getNativeHookType()
    {
        return self::TYPE_FILTER;
    }
}
