<?php

class Flagship_Filters
{
    protected $flagship;

    public function __construct(Flagship_Application $flagship)
    {
        $this->flagship = $flagship;
    }

    public static function add($filter_name, $optional_method_name = false)
    {
        $method = $optional_method_name ? $optional_method_name : $filter_name.'_filter';

        if (method_exists(__CLASS__, $method)) {
            $rf = new ReflectionMethod(__CLASS__, $method);

            add_filter($filter_name, array(__CLASS__, $method), 10, $rf->getNumberOfParameters());

            return;
        }

        throw new Exception('Attempt to use undefined filter: '.__CLASS__.'::'.$method);
    }

    public static function woocommerce_settings_api_sanitized_fields_flagship_shipping_method_filter($sanitized_fields)
    {
        $sanitized_fields = apply_filters('settings_sanitized_fields_enabled', $sanitized_fields);
        $sanitized_fields = apply_filters('settings_sanitized_fields_address', $sanitized_fields);
        $sanitized_fields = apply_filters('settings_sanitized_fields_shipper_credentials', $sanitized_fields);

        return $sanitized_fields;
    }

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
