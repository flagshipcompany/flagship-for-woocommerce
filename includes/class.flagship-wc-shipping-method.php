<?php

class Flagship_WC_Shipping_Method extends WC_Shipping_Method
{
    protected $app;

    /**
     * Constructor for your shipping class.
     */
    public function __construct()
    {
        $this->id = FLAGSHIP_SHIPPING_PLUGIN_ID; // Id for your shipping method. Should be uunique.
        $this->method_title = __('Flagship Shipping');  // Title shown in admin
        $this->method_description = __('Obtains real time shipping rates via Flagship Shipping API'); // Description shown in admin

        $this->title = 'Flagship Shipping'; // This can be added as an setting but for this example its forced.

        // flagship options
        $this->enabled = $this->get_option('enabled');
        $this->token = $this->get_option('token');
        $this->required_address = $this->get_option('shipping_cost_requires_address', 'no');

        // flagship app
        $this->flagship = Flagship_Application::get_instance();

        $this->init();
    }

    /**
     * Init your settings.
     */
    public function init()
    {
        // Load the settings API
        $this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
        $this->init_settings(); // This is part of the settings API. Loads settings you previously init.

        // Save settings in admin if you have any defined
        add_action('woocommerce_update_options_shipping_'.$this->id, array($this, 'process_admin_options'));
        
        // validate settings before save
        add_filter('woocommerce_settings_api_sanitized_fields_' . $this->id, array($this, 'sanitized_fields_filter'));
        // 
        add_filter('sanitized_fields_enabled', array($this, 'sanitized_fields_enabled_filter'), 10, 1);
        add_filter('sanitized_fields_address', array($this, 'sanitized_fields_address_filter'), 10, 1);

    }

    // alerts
    public function admin_options()
    {
        global $current_section;

        if ($current_section == 'flagship_wc_shipping_method') {
            $this->flagship->notification->view();
        }

        parent::admin_options();
    }

    // filters
    // 
    // on save settings
    public function sanitized_fields_filter($sanitized_fields)
    {
        $sanitized_fields = apply_filters('sanitized_fields_enabled', $sanitized_fields);
        // token is validated when address is validated
        $sanitized_fields = apply_filters('sanitized_fields_address', $sanitized_fields);

        return $sanitized_fields;
    }

    public function sanitized_fields_enabled_filter($sanitized_fields)
    {
        if ($sanitized_fields['enabled'] != 'yes') {
            $this->flagship->notification->add('warning', __('Flagship Shipping is disabled.', 'flagship-shipping'));
        }

        return $sanitized_fields;
    }

    public function sanitized_fields_address_filter($sanitized_fields)
    {
        $errors = $this->flagship->validation->address(
            $sanitized_fields['origin'],
            $sanitized_fields['freight_shipper_state'],
            $sanitized_fields['freight_shipper_city']
        );

        // address correction
        if ($errors && isset($errors['content'])) {
            $sanitized_fields['origin'] = $errors['content']['postal_code'];
            $sanitized_fields['freight_shipper_state'] = $errors['content']['state'];
            $sanitized_fields['freight_shipper_city'] = $errors['content']['city'];

            $this->flagship->notification->add('warning', __('Address corrected to match with shipper\'s postal code.', 'flagship-shipping'));

            $errors = array();
        }

        if ($errors) {
            $this->flagship->notification->add('warning', $errors);
        }

        return $sanitized_fields;
    }

    /**
     * calculate_shipping function.
     *
     * @param mixed $package
     */
    public function calculate_shipping($package)
    {
        $quote = array(
            'from' => array(),
            'to' => array(),
            'packages' => array(
                'items' => array(),
                'units' => 'imperial',
                'type' => 'package',
            ),
            'payment' => array(
                'payer' => 'F',
            ),
        );

        $client = $this->flagship->client();

        $fromAddress = array(
            'city' => 'MONTREAL',
            'country' => 'CA',
            'state' => 'QC',
            'postal_code' => 'H7W3C3',
        );

        $response = $client->get('/addresses/integrity', $fromAddress);

        console($response);

        $rate = array(
            'id' => $this->id,
            'label' => $this->title,
            'cost' => '10.99',
            'calc_tax' => 'per_item',
        );

        // Register the rate
        $this->add_rate($rate);
        $this->add_rate($rate);
    }

    public function init_form_fields()
    {
        $this->form_fields = array(
            'enabled' => array(
                'title' => __('Enable Flagship Shipping', 'flagship-shipping'),
                'type' => 'checkbox',
                'label' => __('Enable this shipping method', 'flagship-shipping'),
                'default' => 'no',
            ),
            'title' => array(
                'title' => __('Method Title', 'flagship-shipping'),
                'type' => 'text',
                'description' => __('This controls the title which the user sees during checkout.', 'flagship-shipping'),
                'default' => __('Flagship Shipping', 'flagship-shipping'),
                'desc_tip' => true,
            ),
            'token' => array(
                'title' => __('Smartship Access Token', 'flagship-shipping'),
                'type' => 'text',
                'description' => __('After <a href="https://smartship.flagshipcompany.com/company/register">signup</a>, get a <a target="_blank" href="https://auth.smartship.io/tokens/">access token here</a>.', 'flagship-shipping'),
                'default' => '',
            ),
            'offer_rates' => array(
                'title' => __('Offer Rates', 'flagship-shipping'),
                'type' => 'select',
                'description' => '',
                'default' => 'all',
                'options' => array(
                    'all' => __('Offer the customer all returned rates', 'flagship-shipping'),
                    'cheapest' => __('Offer the customer the cheapest rate only, anonymously', 'flagship-shipping'),
                ),
            ),
            'origin' => array(
                'title' => __('Shipper Postal Code', 'flagship-shipping'),
                'type' => 'text',
                'description' => __('Enter valid <strong>Canadian</strong> postcode for the <strong>Shipper</strong>.', 'flagship-shipping'),
                'default' => '',
            ),
            'freight_shipper_city' => array(
                'title' => __('Shipper City', 'flagship-shipping'),
                'type' => 'text',
                'default' => '',
                'description' => 'Required for label Printing. And should be filled if LTL Freight is enabled.',
            ),
            'freight_shipper_state' => array(
                'title' => __('Shipper Province', 'flagship-shipping'),
                'type' => 'select',
                'default' => '',
                'options' => array(
                    'AB' => __('Alberta', 'flagship-shipping'),
                    'BC' => __('British Columbia', 'flagship-shipping'),
                    'MB' => __('Manitoba', 'flagship-shipping'),
                    'NB' => __('New Brunswick', 'flagship-shipping'),
                    'NL' => __('NewFoundland & Labrador', 'flagship-shipping'),
                    'NT' => __('Northwest Territories', 'flagship-shipping'),
                    'NS' => __('Nova Scotia', 'flagship-shipping'),
                    'NU' => __('Nunavut', 'flagship-shipping'),
                    'ON' => __('Ontario', 'flagship-shipping'),
                    'PE' => __('Prince Edward Island', 'flagship-shipping'),
                    'QC' => __('Quebec', 'flagship-shipping'),
                    'SK' => __('Saskatchwen', 'flagship-shipping'),
                    'YT' => __('Yukon', 'flagship-shipping'),
                ),
                'description' => 'Required for label Printing. And should be filled if LTL Freight is enabled.',
            ),
            'shipper_person_name' => array(
                    'title' => __('Shipper Person Name', 'flagship-shipping'),
                    'type' => 'text',
                    'default' => '',
                    'description' => 'Required for label Printing',
            ),
            'shipper_company_name' => array(
                    'title' => __('Shipper Company Name', 'flagship-shipping'),
                    'type' => 'text',
                    'default' => '' ,
                    'description' => 'Required for label Printing',
            ),
            'shipper_phone_number' => array(
                    'title' => __('Shipper Phone Number', 'flagship-shipping'),
                    'type' => 'text',
                    'default' => '' ,
                    'description' => 'Required for label Printing',
            ),
            'shipper_phone_ext' => array(
                    'title' => __('Shipper Phone Extension Number', 'flagship-shipping'),
                    'type' => 'text',
                    'default' => '' ,
                    'description' => 'Required for label Printing',
            ),
            'freight_shipper_street' => array(
                'title' => __('Shipper Street Address', 'flagship-shipping'),
                'type' => 'text',
                'default' => '',
                'description' => 'Required for label Printing. And should be filled if LTL Freight is enabled.',
            ),
            'shipper_residential' => array(
                'title' => __('Residential', 'flagship-shipping'),
                'label' => __('Shipper Address is Residential?', 'flagship-shipping'),
                'type' => 'checkbox',
                'default' => 'no',
                'description' => 'Required for label Printing. And should be filled if LTL Freight is enabled.',
            ),
        );
    }
}
