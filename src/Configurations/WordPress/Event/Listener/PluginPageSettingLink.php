<?php

namespace FS\Configurations\WordPress\Event\Listener;

use FS\Injection\I;

class PluginPageSettingLink extends \FS\Components\AbstractComponent implements \FS\Context\ApplicationListenerInterface, \FS\Configurations\WordPress\Event\NativeHookInterface
{
    public function getSupportedEvent()
    {
        return 'FS\\Configurations\\WordPress\\Event\\PluginPageSettingLinkEvent';
    }

    public function onApplicationEvent(
        \FS\Context\ApplicationEventInterface $event,
        \FS\Context\ConfigurableApplicationContextInterface $context
    ) {
        $links = $event->getInput('links');

        if ($event->getInput('file') == I::basename()) {
            array_unshift($links, $context->getComponent('\\FS\\Components\\Html')->a('flagship_shipping_settings', __('Settings', FLAGSHIP_SHIPPING_TEXT_DOMAIN), array(
                'escape' => true,
                'target' => true,
            )));
        }

        return $links;
    }

    public function publishNativeHook(\FS\Context\ConfigurableApplicationContextInterface $context)
    {
        \add_filter('plugin_action_links_'.I::basename(), function ($links, $file) use ($context) {
            $event = new \FS\Configurations\WordPress\Event\PluginPageSettingLinkEvent();
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
