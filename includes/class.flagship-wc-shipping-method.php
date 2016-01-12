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
                'description' => __('After signup, get a <a target="_blank" href="https://auth.smartship.io/tokens/">access token here</a>.', 'flagship-shipping'),
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
                'title' => __('Origin Postcode', 'flagship-shipping'),
                'type' => 'text',
                'description' => __('Enter postcode for the <strong>Shipper</strong>.', 'flagship-shipping'),
                'default' => '',
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
            'freight_shipper_city' => array(
                'title' => __('Shipper City', 'flagship-shipping'),
                'type' => 'text',
                'default' => '',
                'description' => 'Required for label Printing. And should be filled if LTL Freight is enabled.',
            ),
            'freight_shipper_state' => array(
                'title' => __('Shipper State Code', 'flagship-shipping'),
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
