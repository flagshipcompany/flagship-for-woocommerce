<?php

namespace FS\Components\Hook;

class SettingsFilters extends Engine implements Factory\HookRegisterAwareInterface
{
    protected $type = 'filter';
    protected $count = 0;

    public function register()
    {
        $settings = $this->getApplicationContext()->getComponent('\\FS\\Components\\Settings');

        // validate settings before save
        $this->add(
            'woocommerce_shipping_'.$settings['FLAGSHIP_SHIPPING_PLUGIN_ID'].'_instance_settings_values',
            'onSaveOptions'
        );

        // we need to include html, thus remove tag sanitizer
        $this->external->remove('woocommerce_shipping_rate_label', 'sanitize_text_field');
    }

    public function onSaveOptions($sanitized_fields, $shippingMethod)
    {
        $notifier = $this->getApplicationContext()->getComponent('\\FS\\Components\\Notifier');
        $validator = $this->getApplicationContext()->getComponent('\\FS\\Components\\Validation\\SettingsValidator');

        $sanitized_fields = $validator->validate(
            $sanitized_fields,
            $notifier
        );

        return $sanitized_fields;
    }
}
