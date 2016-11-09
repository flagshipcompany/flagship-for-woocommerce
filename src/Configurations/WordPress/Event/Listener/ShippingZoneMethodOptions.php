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
        $fields = $event->getInput('fields');

        $notifier = $context->getComponent('\\FS\\Components\\Notifier');
        $factory = $context->getComponent('\\FS\\Components\\Validation\\Factory\\ValidatorFactory');
        $validator = $factory->getValidator('Settings');
        $request = $context->getComponent('\\FS\\Components\\Web\\RequestParam');

        $fields = $validator->validate(
            $fields,
            $notifier
        );

        $fields['package_box'] = array();

        // add package box
        if ($request->request->get('package_box_model_name')) {
            $modelName = array_map('wc_clean', $request->request->get('package_box_model_name'));
            $length = array_map('wc_clean', $request->request->get('package_box_length'));
            $width = array_map('wc_clean', $request->request->get('package_box_width'));
            $height = array_map('wc_clean', $request->request->get('package_box_height'));
            $weight = array_map('wc_clean', $request->request->get('package_box_weight'));
            $maxWeight = array_map('wc_clean', $request->request->get('package_box_max_weight'));

            foreach ($modelName as $i => $name) {
                if (!isset($modelName[$i])) {
                    continue;
                }

                $fields['package_box'][] = array(
                    'model_name' => $modelName[ $i ],
                    'length' => $length[ $i ],
                    'width' => $width[ $i ],
                    'height' => $height[ $i ],
                    'weight' => $weight[ $i ],
                    'max_weight' => $maxWeight[ $i ],
                );
            }
        }

        $context->debug($fields);

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

        \add_filter('woocommerce_settings_api_sanitized_fields_'.$settings['FLAGSHIP_SHIPPING_PLUGIN_ID'], function ($fields) use ($context) {
            $event = new \FS\Configurations\WordPress\Event\ShippingZoneMethodOptionsEvent();
            $event->setInputs(array(
                'fields' => $fields,
            ));

            return $context->publishEvent($event);
        }, 10, 1);

        // we need to include html, thus remove tag sanitizer
        \remove_filter('woocommerce_shipping_rate_label', 'sanitize_text_field');
    }

    public function getNativeHookType()
    {
        return self::TYPE_FILTER;
    }
}
