<?php

namespace FS\Components\Event\Listener;

use FS\Components\AbstractComponent;
use FS\Context\ApplicationListenerInterface;
use FS\Components\Event\NativeHookInterface;
use FS\Context\ConfigurableApplicationContextInterface as Context;
use FS\Context\ApplicationEventInterface as Event;
use FS\Components\Event\ApplicationEvent;

class ShippingZoneMethodOptions extends AbstractComponent implements ApplicationListenerInterface, NativeHookInterface
{
    public function getSupportedEvent()
    {
        return ApplicationEvent::SHIPPING_ZONE_METHOD_OPTIONS;
    }

    public function onApplicationEvent(Event $event, Context $context)
    {
        $fields = $event->getInput('fields');

        $validator = $context->factory('\\FS\\Components\\Validation\\Factory\\ValidatorFactory')->resolve('Settings');
        $request = $context->_('\\FS\\Components\\Web\\RequestParam');

        $fields = $validator->validate(
            $fields,
            $context
        );

        $fields['package_box'] = array();

        $requestFields = $request->request->all();
        $modelNames = $this->findPackageFieldsFromRequest($requestFields['data'], '/^package_box_model_name/');

        // add package box
        if ($modelNames) {
            $modelName = array_map('wc_clean', $modelNames);
            $length = array_map('wc_clean', $this->findPackageFieldsFromRequest($requestFields['data'], '/^package_box_length/'));
            $width = array_map('wc_clean', $this->findPackageFieldsFromRequest($requestFields['data'], '/^package_box_width/'));
            $height = array_map('wc_clean', $this->findPackageFieldsFromRequest($requestFields['data'], '/^package_box_height/'));
            $weight = array_map('wc_clean', $this->findPackageFieldsFromRequest($requestFields['data'], '/^package_box_weight/'));
            $maxWeight = array_map('wc_clean', $this->findPackageFieldsFromRequest($requestFields['data'], '/^package_box_max_weight/'));

            foreach ($modelName as $i => $name) {
                if (!isset($modelName[$i]) || !$this->checkPositiveNumber($length[$i]) || !$this->checkPositiveNumber($width[$i]) || !$this->checkPositiveNumber($height[$i]) || !$this->checkPositiveNumber($weight[$i]) || !$this->checkPositiveNumber($maxWeight[$i])) {
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

        return $fields;
    }

    public function publishNativeHook(Context $context)
    {
        $id = $context->setting('FLAGSHIP_SHIPPING_PLUGIN_ID');

        \add_filter('woocommerce_shipping_'.$id.'_instance_settings_values', function ($fields, $method) use ($context) {
            $event = new ApplicationEvent(ApplicationEvent::SHIPPING_ZONE_METHOD_OPTIONS);
            $event->setInputs(array(
                'fields' => $fields,
                'method' => $method,
            ));

            return $context->publishEvent($event);
        }, 10, 2);

        \add_filter('woocommerce_settings_api_sanitized_fields_'.$id, function ($fields) use ($context) {
            $event = new ApplicationEvent(ApplicationEvent::SHIPPING_ZONE_METHOD_OPTIONS);
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

    protected function findPackageFieldsFromRequest(array $requestFields, $pattern)
    {
        $keyMatches = preg_grep($pattern, array_keys($requestFields));

        return array_values(array_intersect_key($requestFields, array_flip($keyMatches)));
    }

    protected function checkPositiveNumber($input)
    {
        if (!is_numeric($input) || $input <= 0) {
            return false;
        }

        return true;
    }
}
