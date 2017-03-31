<?php

namespace FS\Components\Event\Listener;

class ShippingZoneMethodAdd extends \FS\Components\AbstractComponent implements \FS\Context\ApplicationListenerInterface, \FS\Configurations\WordPress\Event\NativeHookInterface
{
    public function getSupportedEvent()
    {
        return 'FS\\Configurations\\WordPress\\Event\\ShippingZoneMethodAddEvent';
    }

    public function onApplicationEvent(
        \FS\Context\ApplicationEventInterface $event,
        \FS\Context\ConfigurableApplicationContextInterface $context
    ) {
        $instanceId = $event->getInput('instanceId');

        $options = $context->getComponent('\\FS\\Components\\Options');
        $settings = $context->getComponent('\\FS\\Components\\Settings');

        \update_option('woocommerce_'.$settings['FLAGSHIP_SHIPPING_PLUGIN_ID'].'_'.$instanceId.'_settings', $options->all());
    }

    public function publishNativeHook(\FS\Context\ConfigurableApplicationContextInterface $context)
    {
        \add_action('woocommerce_shipping_zone_method_added', function ($instanceId, $type, $zoneId) use ($context) {
            $settings = $context->getComponent('\\FS\\Components\\Settings');
            $options = $context->getComponent('\\FS\\Components\\Options');

            if ($type == $settings['FLAGSHIP_SHIPPING_PLUGIN_ID'] && $options->all()) {
                $event = new \FS\Configurations\WordPress\Event\ShippingZoneMethodAddEvent();
                $event->setInputs(array(
                    'instanceId' => $instanceId,
                ));

                $context->publishEvent($event);
            }
        }, 10, 3);
    }

    public function getNativeHookType()
    {
        return self::TYPE_ACTION;
    }
}
