<?php

class Flagship_Filters extends Flagship_Api_Hooks
{
    public function add($filter_name, $optional_method_name = false)
    {
        return $this->add_hook('filter', $filter_name, $optional_method_name);
    }

    public function remove($filter_name, $method = null)
    {
        return $this->remove_hook('filter', $filter_name, $method);
    }

    public function has($filter_name, $optional_method_name = false)
    {
        return $this->has_hook('filter', $filter_name, $optional_method_name);
    }

    public function apply($filter_name, $args = array())
    {
        apply_filters_ref_array($filter_name, $args);
    }

    // built-in filters
    public static function woocommerce_shipping_methods_filter($methods)
    {
        $methods[] = 'Flagship_WC_Shipping_Method';

        return $methods;
    }

    // MAP TO 'plugin_action_links_'.FLS__PLUGIN_BASENAME
    //
    public static function plugin_page_setting_links_action($links, $file)
    {
        if ($file == FLS__PLUGIN_BASENAME) {
            array_unshift($links, Flagship_Html::anchor('flagship_shipping_settings', 'Settings', array(
                'escape' => true,
                'target' => true,
            )));
        }

        return $links;
    }

    public static function woocommerce_settings_api_sanitized_fields_flagship_shipping_method_filter($sanitized_fields)
    {
        $sanitized_fields = apply_filters('settings_sanitized_fields_enabled', $sanitized_fields);
        $sanitized_fields = apply_filters('settings_sanitized_fields_address', $sanitized_fields);
        $sanitized_fields = apply_filters('settings_sanitized_fields_shipper_credentials', $sanitized_fields);

        return $sanitized_fields;
    }

    // custom filters
    public static function settings_sanitized_fields_enabled_filter($sanitized_fields)
    {
        $flagship = Flagship_Application::get_instance();

        if ($sanitized_fields['enabled'] != 'yes') {
            $flagship->notification->add('warning', __('Flagship Shipping is disabled.', 'flagship-shipping'));
        }

        return $sanitized_fields;
    }

    public static function settings_sanitized_fields_address_filter($sanitized_fields)
    {
        $flagship = Flagship_Application::get_instance();

        $errors = $flagship->validation->address(
            $sanitized_fields['origin'],
            $sanitized_fields['freight_shipper_state'],
            $sanitized_fields['freight_shipper_city']
        );

        // address correction
        if ($errors && isset($errors['content'])) {
            $sanitized_fields['origin'] = $errors['content']['postal_code'];
            $sanitized_fields['freight_shipper_state'] = $errors['content']['state'];
            $sanitized_fields['freight_shipper_city'] = $errors['content']['city'];

            $flagship->notification->add('warning', __('Address corrected to match with shipper\'s postal code.', 'flagship-shipping'));

            $errors = array();
        }

        if ($errors) {
            $flagship->notification->add('warning', $errors);
        }

        return $sanitized_fields;
    }

    public static function settings_sanitized_fields_shipper_credentials_filter($sanitized_fields)
    {
        $flagship = Flagship_Application::get_instance();

        if (!$sanitized_fields['shipper_person_name']) {
            $flagship->notification->add('warning', __('Shipper person name is missing.', 'flagship-shipping'));
        }

        if (!$sanitized_fields['shipper_company_name']) {
            $flagship->notification->add('warning', __('Shipper company name is missing.', 'flagship-shipping'));
        }

        if (!$sanitized_fields['shipper_phone_number']) {
            $flagship->notification->add('warning', __('Shipper phone number is missing.', 'flagship-shipping'));
        }

        if (!$sanitized_fields['freight_shipper_street']) {
            $flagship->notification->add('warning', __('Shipper address\'s streetline is missing.', 'flagship-shipping'));
        }

        return $sanitized_fields;
    }
}
