<?php

namespace FS\Configurations\WordPress\Event\Listener;

class ShippingZoneMethodOptions extends \FS\Components\AbstractComponent implements \FS\Context\ApplicationListenerInterface, \FS\Configurations\WordPress\Event\NativeHookInterface
{
    public function getSupportedEvent()
    {
        return 'FS\\Configurations\\WordPress\\Event\\ShippingZoneMethodOptionsEvent';
    }

    public function onApplicationEvent(
        \FS\Context\ApplicationEventInterface $event,
        \FS\Context\ConfigurableApplicationContextInterface $context
    ) {
        $fields = $event->getInputs()['fields'];

        $notifier = $context->getComponent('\\FS\\Components\\Notifier');
        $validator = $context->getComponent('\\FS\\Components\\Validation\\SettingsValidator');

        $fields = $validator->validate(
            $fields,
            $notifier
        );

        return $fields;
    }

    public function publishNativeHook(\FS\Context\ConfigurableApplicationContextInterface $context)
    {
        $settings = $context->getComponent('\\FS\\Components\\Settings');

        \add_filter('woocommerce_shipping_'.$settings['FLAGSHIP_SHIPPING_PLUGIN_ID'].'_instance_settings_values', function ($fields, $method) use ($context) {
            $event = new \FS\Configurations\WordPress\Event\ShippingZoneMethodOptionsEvent();
            $event->setInputs(array(
                'fields' => $fields,
                'method' => $method,
            ));

            return $context->publishEvent($event);
        }, 10, 2);

        // we need to include html, thus remove tag sanitizer
        \remove_filter('woocommerce_shipping_rate_label', 'sanitize_text_field');
    }

    public function getNativeHookType()
    {
        return self::TYPE_FILTER;
    }
}
