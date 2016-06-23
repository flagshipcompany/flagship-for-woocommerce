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

        // providers
        $this->flagship->register('Quoter');

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
        //

        // filters
        $this->flagship['hooks']->load('settings.filters', 'Settings_Filters');
    }

    /**
     * add notifications section on top of settings.
     */
    public function admin_options()
    {
        global $current_section;

        if ($current_section == 'flagship_wc_shipping_method') {
            $this->flagship['notification']->view();
        }

        parent::admin_options();
    }

    /**
     * calculate_shipping function.
     *
     * @param mixed $package
     */
    public function calculate_shipping($package)
    {
        $rates = $this->flagship['quoter']->quote($package);

        $offer_rates = $this->get_option('offer_rates');

        if ($offer_rates == 'all') {
            foreach ($rates as $rate) {
                $this->add_rate($rate);
            }

            return;
        }

        if ($offer_rates == 'cheapest') {
            $this->add_rate($rates[0]);

            return;
        }

        $count = intval($offer_rates);

        while ($count > 0 && $rates) {
            $rate = array_shift($rates);
            $this->add_rate($rate);

            --$count;
        }
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
                'custom_attributes' => array(
                    'maxlength' => 255,
                ),
            ),
            'offer_rates' => array(
                'title' => __('Offer Rates', 'flagship-shipping'),
                'type' => 'select',
                'class' => 'wc-enhanced-select',
                'description' => '',
                'default' => 'all',
                'options' => array(
                    'all' => __('Offer the customer all returned rates', 'flagship-shipping'),
                    'cheapest' => __('Offer the customer the cheapest rate only', 'flagship-shipping'),
                    '2' => __('2 cheapest rates', 'flagship-shipping'),
                    '3' => __('3 cheapest rates', 'flagship-shipping'),
                    '4' => __('4 cheapest rates', 'flagship-shipping'),
                    '5' => __('5 cheapest rates', 'flagship-shipping'),
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
                'description' => 'Required',
            ),
            'freight_shipper_state' => array(
                'title' => __('Shipper Province', 'flagship-shipping'),
                'type' => 'select',
                'class' => 'wc-enhanced-select',
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
                'description' => 'Required',
            ),
            'shipper_person_name' => array(
                'title' => __('Shipper Person Name', 'flagship-shipping'),
                'type' => 'text',
                'default' => '',
                'description' => 'Required, maximum 21 characters',
                'custom_attributes' => array(
                    'maxlength' => 21,
                ),
            ),
            'shipper_company_name' => array(
                    'title' => __('Shipper Company Name', 'flagship-shipping'),
                    'type' => 'text',
                    'default' => '',
                    'description' => 'Required',
                    'custom_attributes' => array(
                        'maxlength' => 30,
                    ),
            ),
            'shipper_phone_number' => array(
                    'title' => __('Shipper Phone Number', 'flagship-shipping'),
                    'type' => 'text',
                    'default' => '',
                    'description' => 'Required',
            ),
            'shipper_phone_ext' => array(
                    'title' => __('Shipper Phone Extension Number', 'flagship-shipping'),
                    'type' => 'text',
                    'default' => '',
                    'description' => 'Optional, if applicable',
            ),
            'freight_shipper_street' => array(
                'title' => __('Shipper Street Address', 'flagship-shipping'),
                'type' => 'text',
                'default' => '',
                'description' => 'Required',
            ),
            'shipper_residential' => array(
                'title' => __('Residential', 'flagship-shipping'),
                'label' => __('Shipper Address is Residential?', 'flagship-shipping'),
                'type' => 'checkbox',
                'default' => 'no',
            ),
            'default_package_box_split' => array(
                'title' => __('Box Split', 'flagship-shipping'),
                'label' => __('Everything in one package box?', 'flagship-shipping'),
                'type' => 'checkbox',
                'default' => 'no',
            ),
            'default_package_box_split_weight' => array(
                'title' => __('Box Split Weight', 'flagship-shipping'),
                'description' => __('Maximun weight per each package box (lbs)', 'flagship-shipping'),
                'type' => 'text',
                'default' => 20,
            ),
            'default_shipping_markup_type' => array(
                'title' => __('Shipping Cost Markup Type', 'flagship-shipping'),
                'description' => __('Shipping Cost Markup Type can be either flat rate (i.e. dollar valued) or percentage rate (i.e. rate based on certain percentage)', 'flagship-shipping'),
                'type' => 'select',
                'class' => 'wc-enhanced-select',
                'options' => array(
                    'flat_rate' => __('Flat rate', 'flagship-shipping'),
                    'percentage' => __('Percentage', 'flagship-shipping'),
                ),
                'default' => 'percentage',
            ),
            'default_shipping_markup' => array(
                'title' => __('Shipping Cost Markup', 'flagship-shipping'),
                'type' => 'text',
                'default' => 0,
            ),
            'default_pickup_time_from' => array(
                'title' => __('Pick-up Start Time', 'flagship-shipping'),
                'type' => 'select',
                'class' => 'wc-enhanced-select',
                'options' => array(
                    '09:00' => '09:00',
                    '10:00' => '10:00',
                    '11:00' => '11:00',
                    '12:00' => '12:00',
                    '13:00' => '13:00',
                    '14:00' => '14:00',
                    '15:00' => '15:00',
                    '16:00' => '16:00',
                    '17:00' => '17:00',
                ),
                'default' => '09:00',
            ),
            'default_pickup_time_to' => array(
                'title' => __('Pick-up End Time', 'flagship-shipping'),
                'type' => 'select',
                'class' => 'wc-enhanced-select',
                'options' => array(
                    '09:00' => '09:00',
                    '10:00' => '10:00',
                    '11:00' => '11:00',
                    '12:00' => '12:00',
                    '13:00' => '13:00',
                    '14:00' => '14:00',
                    '15:00' => '15:00',
                    '16:00' => '16:00',
                    '17:00' => '17:00',
                ),
                'default' => '17:00',
            ),
            'default_shipping_email' => array(
                'title' => __('Contact Email', 'flagship-shipping'),
                'type' => 'text',
                'default' => get_option('admin_email'),
            ),
            'disable_courier_fedex' => array(
                'title' => __('Disable FedEx Rates', 'flagship-shipping'),
                'type' => 'checkbox',
                'default' => 'no',
            ),
            'disable_courier_ups' => array(
                'title' => __('Disable UPS Rates', 'flagship-shipping'),
                'type' => 'checkbox',
                'default' => 'no',
            ),
            'disable_courier_purolator' => array(
                'title' => __('Disable Purolator Rates', 'flagship-shipping'),
                'type' => 'checkbox',
                'default' => 'no',
            ),
        );
    }
}
