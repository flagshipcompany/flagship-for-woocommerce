<?php

class FlagShip_WC_Shipping_Method extends WC_Shipping_Method
{
    protected $app;

    /**
     * Constructor for your shipping class.
     */
    public function __construct()
    {
        // flagship app
        $this->ctx = FlagShip_Application::get_instance();

        $this->id = FLAGSHIP_SHIPPING_PLUGIN_ID; // Id for your shipping method. Should be uunique.
        $this->method_title = __('FlagShip Shipping', FLAGSHIP_SHIPPING_TEXT_DOMAIN);  // Title shown in admin
        $this->method_description = __('Obtains real time shipping rates via FlagShip Shipping API', FLAGSHIP_SHIPPING_TEXT_DOMAIN); // Description shown in admin

        $this->title = __('FlagShip Shipping', FLAGSHIP_SHIPPING_TEXT_DOMAIN); // This can be added as an setting but for this example its forced.

        // flagship options
        $this->enabled = $this->get_option('enabled');
        $this->token = $this->get_option('token');
        $this->required_address = $this->get_option('shipping_cost_requires_address', 'no');
        $this->init();

        // providers
        $this->ctx->load('Quoter');
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

        load_plugin_textdomain(FLAGSHIP_SHIPPING_TEXT_DOMAIN, false, 'flagship-for-woocommerce/languages');

        // filters
        $this->ctx['hooks']->load('settings.filters', 'Settings_Filters');
    }

    /**
     * add notifications section on top of settings.
     */
    public function admin_options()
    {
        global $current_section;

        if ($current_section == 'flagship_wc_shipping_method') {
            $this->ctx['notification']->view();
        }

        parent::admin_options();
    }

    /**
     * calculate_shipping function.
     *
     * @param array $package
     */
    public function calculate_shipping($package = array())
    {
        // we want to avoid redundant quote request
        // the tradeoff: rates rely on 1st time of quote. if certain courier is missing, we cannot
        // retrieve it again unless cart changed.
        $cart = WC()->session->get('cart');
        $serialized = serialize($cart);
        $hash = md5($serialized);
        $key = 'flagship_shipping_quote_rates_'.$hash;

        $rates = WC()->session->get($key);

        if (!$rates) {
            $rates = $this->ctx['quoter']->quote($package);

            WC()->session->set($key, $rates);
        }

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
                'title' => __('FlagShip Shipping', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'checkbox',
                'label' => __('Enable this shipping method', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'default' => 'no',
            ),
            'title' => array(
                'title' => __('Method Title', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'text',
                'description' => __('This controls the title which the user sees during checkout.', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'default' => __('FlagShip Shipping', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'desc_tip' => true,
            ),
            'token' => array(
                'title' => __('Smartship Access Token', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'text',
                'description' => __('After <a href="https://smartship.flagshipcompany.com/company/register">signup</a>, get a <a target="_blank" href="https://auth.smartship.io/tokens/">access token here</a>.', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'default' => '',
                'custom_attributes' => array(
                    'maxlength' => 255,
                ),
            ),
            'offer_rates' => array(
                'title' => __('Offer Rates', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'select',
                'class' => 'wc-enhanced-select',
                'description' => '',
                'default' => 'all',
                'options' => array(
                    'all' => __('Offer the customer all returned rates', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                    'cheapest' => __('Offer the customer the cheapest rate only', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                    '2' => __('2 cheapest rates', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                    '3' => __('3 cheapest rates', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                    '4' => __('4 cheapest rates', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                    '5' => __('5 cheapest rates', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                ),
            ),
            'origin' => array(
                'title' => __('Shipper Postal Code', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'text',
                'description' => __('Enter valid <strong>Canadian</strong> postcode for the <strong>Shipper</strong>.', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'default' => '',
            ),
            'freight_shipper_city' => array(
                'title' => __('Shipper City', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'text',
                'default' => '',
                'description' => __('Required', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
            ),
            'freight_shipper_state' => array(
                'title' => __('Shipper Province', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'select',
                'class' => 'wc-enhanced-select',
                'default' => '',
                'options' => array(
                    'AB' => __('Alberta', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                    'BC' => __('British Columbia', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                    'MB' => __('Manitoba', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                    'NB' => __('New Brunswick', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                    'NL' => __('NewFoundland & Labrador', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                    'NT' => __('Northwest Territories', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                    'NS' => __('Nova Scotia', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                    'NU' => __('Nunavut', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                    'ON' => __('Ontario', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                    'PE' => __('Prince Edward Island', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                    'QC' => __('Quebec', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                    'SK' => __('Saskatchwen', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                    'YT' => __('Yukon', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                ),
                'description' => __('Required', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
            ),
            'shipper_person_name' => array(
                'title' => __('Shipper Person Name', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'text',
                'default' => '',
                'description' => __('Required, maximum 21 characters', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'custom_attributes' => array(
                    'maxlength' => 21,
                ),
            ),
            'shipper_company_name' => array(
                    'title' => __('Shipper Company Name', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                    'type' => 'text',
                    'default' => '',
                    'description' => __('Required', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                    'custom_attributes' => array(
                        'maxlength' => 30,
                    ),
            ),
            'shipper_phone_number' => array(
                    'title' => __('Shipper Phone Number', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                    'type' => 'text',
                    'default' => '',
                    'description' => __('Required', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
            ),
            'shipper_phone_ext' => array(
                    'title' => __('Shipper Phone Extension Number', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                    'type' => 'text',
                    'default' => '',
                    'description' => __('Optional, if applicable', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
            ),
            'freight_shipper_street' => array(
                'title' => __('Shipper Street Address', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'text',
                'default' => '',
                'description' => __('Required', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
            ),
            'shipper_residential' => array(
                'title' => __('Residential', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'label' => __('Shipper Address is Residential?', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'checkbox',
                'default' => 'no',
            ),
            'default_package_box_split' => array(
                'title' => __('Box Split', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'label' => __('Everything in one package box?', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'checkbox',
                'default' => 'no',
            ),
            'default_package_box_split_weight' => array(
                'title' => __('Box Split Weight', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'description' => __('Maximun weight per each package box (lbs)', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'text',
                'default' => 20,
            ),
            'default_shipping_markup_type' => array(
                'title' => __('Shipping Cost Markup Type', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'description' => __('Shipping Cost Markup Type can be either flat rate (i.e. dollar valued) or percentage rate (i.e. rate based on certain percentage)', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'select',
                'class' => 'wc-enhanced-select',
                'options' => array(
                    'flat_rate' => __('Flat rate', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                    'percentage' => __('Percentage', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                ),
                'default' => 'percentage',
            ),
            'default_shipping_markup' => array(
                'title' => __('Shipping Cost Markup', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'text',
                'default' => 0,
            ),
            'default_pickup_time_from' => array(
                'title' => __('Pick-up Start Time', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
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
                'title' => __('Pick-up End Time', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
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
                'title' => __('Contact Email', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'text',
                'default' => get_option('admin_email'),
            ),
            'disable_courier_fedex' => array(
                'title' => __('Disable FedEx Rates', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'checkbox',
                'default' => 'no',
            ),
            'disable_courier_ups' => array(
                'title' => __('Disable UPS Rates', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'checkbox',
                'default' => 'no',
            ),
            'disable_courier_purolator' => array(
                'title' => __('Disable Purolator Rates', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'checkbox',
                'default' => 'no',
            ),
        );
    }
}
