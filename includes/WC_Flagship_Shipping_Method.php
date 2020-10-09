<?php
namespace FlagshipWoocommerce;

use FlagshipWoocommerce\Helpers\Notification_Helper;
use FlagshipWoocommerce\Helpers\Validation_Helper;
use FlagshipWoocommerce\Helpers\Template_Helper;
use FlagshipWoocommerce\Helpers\Menu_Helper;

class WC_Flagship_Shipping_Method extends \WC_Shipping_Method {

    private $token;

    /**
     * @access public
     * @return void
     */
    public function __construct($instance_id = 0) {
        parent::__construct($instance_id);

        $this->id = FlagshipWoocommerceShipping::$methodId;
        $this->method_title = __('FlagShip Shipping', 'flagship-for-woocommerce');
        $this->method_description = __('Obtain FlagShip shipping rates for orders and export order to FlagShip to dispatch shipment', 'flagship-for-woocommerce');
        $this->supports = array(
            'shipping-zones',
            'instance-settings',
            'instance-settings-modal',
            'settings',
        );
        $this->init_method_settings();
        $this->init();
        $this->init_instance_settings();
    }

    /**
     *
     * @access public
     * @return void
     */
    public function init() {
        $this->init_form_fields();
        $this->init_settings();

        add_action( 'woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
        add_filter('woocommerce_settings_api_sanitized_fields_' . $this->id,  array($this, 'validate_admin_options'));
    }

    /**
     * @return void
     */
    public function init_form_fields() {

        $this->form_fields = $this->makeGeneralFields();
        $this->instance_form_fields = $this->makeInstanceFields();
    }

    /**
     * @access public
     * @param mixed $package
     * @return void
     */
    public function calculate_shipping($package = Array()) {
        if (count($package) == 0 || $this->enabled != 'yes') {
            return;
        }

        $settings = array_merge($this->settings, $this->instance_settings);
        $ratesProcessor = new Cart_Rates_Processor($this->id, $this->token, array_merge($settings, array('debug_mode' => $this->debugMode)));
        $rates = $ratesProcessor->fetchRates($package);
        $cartRates = $ratesProcessor->processRates($package, $rates);

        foreach ($cartRates as $key => $rate) {
            $this->add_rate($rate);
        }
    }

    public function get_option($key, $empty_value = null) {
        if ($key === 'tracking_emails' && !array_key_exists('tracking_emails', $this->settings)) {
            return trim(WC()->mailer()->get_emails()['WC_Email_New_Order']->recipient);
        }

        return parent::get_option($key, $empty_value);
    }

    public function validate_admin_options($settings) {
        $testEnv = $settings['test_env'] == 'no' || $settings['test_env'] == null ? 0 : 1;
        $validationHelper = new Validation_Helper($testEnv);

        if(isset($settings['token']) && !empty(trim($settings['token'])) && !$validationHelper->validateToken($settings['token']))
        {
            $settings['token'] = '';
            add_action('admin_notices', array((new Notification_Helper()),'add_token_invalid_notice'));
        }


        if (isset($settings['tracking_emails']) && !empty(trim($settings['tracking_emails'])) && !Validation_Helper::validateMultiEmails($settings['tracking_emails'])) {
            $settings = get_option($this->get_option_key(), array());
            $settings['tracking_emails'] = get_array_value($settings,'tracking_emails', '');

            add_action( 'admin_notices', array((new Notification_Helper()), 'add_tracking_email_invalid_notice'));
        }

        return $settings;
    }

    public function generate_radio_html($key, $data)
    {
        $data['field_name'] = 'woocommerce_'.FlagshipWoocommerceShipping::$methodId.'_'.$key;
        $data['value'] = $this->get_option($key, null);

        return Template_Helper::render_embedded_php('_radio_field.php', $data);
    }

    protected function init_method_settings() {
        $this->enabled = $this->get_option('enabled', 'no');
        $this->title = $this->get_option('title', __('FlagShip Shipping', 'flagship-for-woocommerce'));
        $this->token = $this->get_option('token', '');
        $this->debugMode = $this->get_option('debug_mode', 'no');
    }

    protected function makeGeneralFields() {
        return array(
            'enabled' => array(
                'title' => __('Enable', 'flagship-for-woocommerce'),
                'type' => 'checkbox',
                'description' => __( 'Enable this shipping method', 'flagship-for-woocommerce'),
                'default' => 'no'
            ),
            'test_env' => array(
                'title' => __('Enable Test Environment', 'flagship-for-woocommerce'),
                'type' => 'checkbox',
                'description' => __('Use FlagShip\'s test environment. Any shipments made in the test environment will not be shipped','flagship-for-woocommerce'),
                'default' => 'no'
            ),
            'token' => array(
                'title' => __('FlagShip access token', 'flagship-for-woocommerce'),
                'type' => 'text',
                'description' => sprintf(__('After <a href="%s" target="_blank">signup </a>, <a target="_blank" href="%s">get an access token here </a>.', 'flagship-for-woocommerce'), 'https://www.flagshipcompany.com/sign-up/', 'https://auth.smartship.io/tokens/'),
            ),
            'tracking_emails' => array(
                'title' => __('Tracking emails', 'flagship-for-woocommerce'),
                'type' => 'text',
                'description' => __('The emails (separated by ;) to receive tracking information of shipments.', 'flagship-for-woocommerce'),
            ),
            'box_split' => array(
                'title' => __('Box split', 'flagship-for-woocommerce'),
                'type' => 'radio',
                'description' => __('If enabled, errors will be displayed in the pages showing shipping rates', 'flagship-for-woocommerce'),
                'default' => 'one_box',
                'options' => array(
                    'one_box' => 'Everything in one box',
                    'box_per_item' => 'One box per item',
                    'by_weight' => 'Split by weight',
                    'packing_api' => 'Use FlagShip Packing API to pack items into',
                ),
                'extra_note' =>  array(
                    'packing_api' => sprintf('<a href="%s" target="_blank">%s</a>',admin_url('admin.php?page=flagship/boxes'), __('Boxes','flagship-for-woocommerce')),
                ),
            ),
            'box_split_weight' => array(
                'title' => __('Box split weight', 'flagship-for-woocommerce'),
                'type' => 'decimal',
                'description' => __("Maximum weight in each box (only used when 'Split by weight' is chosen for box split.", 'flagship-for-woocommerce'),
                'css' => 'width:70px;',
            ),
            'debug_mode' => array(
                'title' => __('Debug mode', 'flagship-for-woocommerce'),
                'label' => __( 'Enable debug mode', 'flagship-for-woocommerce' ),
                'type' => 'checkbox',
                'description' => __('If enabled, errors will be displayed in the pages showing shipping rates', 'flagship-for-woocommerce'),
                'default' => 'no'
            ),
        );
    }

    protected function makeInstanceFields() {
        $ecommerceApplicable = $this->isInstanceForEcommerce(\WC_Shipping_Zones::get_zone_by( 'instance_id', $this->instance_id)->get_zone_locations());

        $fields = array(
            'shipping_rates_configs' => array(
                'title' => __('Rates', 'flagship-for-woocommerce'),
                'type' => 'title',
            ),
            'allow_standard_rates' => array(
                'title' => __('Offer standard rates', 'flagship-for-woocommerce'),
                'type' => 'checkbox',
                'default' => 'yes'
            ),
            'allow_express_rates' => array(
                'title' => __('Offer express rates', 'flagship-for-woocommerce'),
                'type' => 'checkbox',
                'default' => 'yes'
            ),
            'offer_dhl_ecommerce_rates' => array(
                'title' => __('Offer DHL ecommerce rates', 'flagship-for-woocommerce'),
                'type' => 'checkbox',
                'description' => __( 'Available for international destinations when package is less than 2kg', 'flagship-for-woocommerce'),
                'default' => 'no'
            ),
            'only_show_cheapest' => array(
                'title' => __('Only show the cheapest rate', 'flagship-for-woocommerce'),
                'type' => 'checkbox',
                'default' => 'no'
            ),
            'dropshipping_address' => array(
                'title' => __('DropShip Address','flagship-for-woocommerce'),
                'type' => 'title',
                'description' => __('Store owner may ship from a warehouse'),
            ),
            'shipping_markup' => array(
                'title' => __('Markup', 'flagship-for-woocommerce'),
                'type' => 'title',
                'description' => __('Store owner may apply additional fee for shipping.', 'flagship-for-woocommerce'),
            ),
            'shipping_cost_markup_percentage' => array(
                'title' => __('Shipping cost markup (%)', 'flagship-for-woocommerce'),
                'type' => 'decimal',
                'description' => __( 'Shipping cost markup in percentage', 'flagship-for-woocommerce'),
                'default' => 0
            ),
            'shipping_cost_markup_flat' => array(
                'title' => __('Shipping cost markup in flat fee ($)', 'flagship-for-woocommerce'),
                'type' => 'decimal',
                'description' => __( 'Shipping cost markup in flat fee (this will be applied after the percentage markup)', 'flagship-for-woocommerce'),
                'default' => 0
            ),
            'shipping_options' => array(
                'title' => __('Shipping Options', 'flagship-for-woocommerce'),
                'type' => 'title',
            ),
            'show_transit_time' => array(
                'title' => __('Show transit time in shopping cart', 'flagship-for-woocommerce'),
                'description' => __('If checked, the transit times of couriers will be shown', 'flagship-for-woocommerce'),
                'type' => 'checkbox',
                'default' => 'no',
            ),
            'signature_required' => array(
                'title' => __('Signature required on delivery', 'flagship-for-woocommerce'),
                'description' => __('If checked, all the shipments to this shipping zone will be signature required on delivery', 'flagship-for-woocommerce'),
                'type' => 'checkbox',
                'default' => 'no',
            ),
            'residential_receiver_address' => array(
                'title' => __('Residential receiver address', 'flagship-for-woocommerce'),
                'description' => __('If checked, all the receiver addresses in this shipping zone will be considered residential', 'flagship-for-woocommerce'),
                'type' => 'checkbox',
                'default' => 'no',
            ),
            'send_tracking_emails' => array(
                'title' => __('Send tracking emails', 'flagship-for-woocommerce'),
                'description' => __('If checked, customers will receive the tracking emails of a shipment.', 'flagship-for-woocommerce'),
                'type' => 'checkbox',
                'default' => 'no',
            ),
        );


        $disableCourierOptions = $this->makeDisableCourierOptions(FlagshipWoocommerceShipping::$couriers, $ecommerceApplicable);
        $fields = array_slice($fields, 0, 5, true) +
           $disableCourierOptions +
            array_slice($fields, 5, NULL, true);

        if (!$ecommerceApplicable) {
            unset($fields['offer_dhl_ecommerce_rates']);
        }

        $fields = array_merge($fields, $this->makeShippingClassSettings());

        $fields = array_slice($fields, 0,10,true) + $this->makeDropShippingAddressFields() + array_slice($fields, 10, NULL, true);

        return $fields;
    }

    protected function makeShippingClassSettings()
    {
        $settings = array();
        $shipping_classes = WC()->shipping()->get_shipping_classes();

        if (empty($shipping_classes)) {
            return $settings;
        }

        $settings['class_costs'] = array(
            'title'       => __( 'Shipping class costs', 'woocommerce' ),
            'type'        => 'title',
            'default'     => '',
            'description' => sprintf( __( 'These costs can optionally be added based on the <a href="%s">product shipping class</a>.', 'woocommerce' ) . ' ' . __('This cost will be applied only once per shipment, regardless of the number of products belonging to that shipping class.', 'flagship-for-woocommerce'),  admin_url( 'admin.php?page=wc-settings&tab=shipping&section=classes' ) ),
        );

        foreach ( $shipping_classes as $shipping_class ) {
            if (!isset( $shipping_class->term_id)) {
                continue;
            }

            $settings[ 'class_cost_' . $shipping_class->term_id ] = array(
                'title'             => sprintf( __( '"%s" shipping class cost', 'woocommerce' ), esc_html( $shipping_class->name ) ),
                'type'              => 'decimal',
                'placeholder'       => __( 'N/A', 'woocommerce' ),
                'description'       => 'shipping class cost',
                'default'           => $this->get_option( 'class_cost_' . $shipping_class->slug ),
                'desc_tip'          => true,
                'sanitize_callback' => array( $this, 'sanitize_cost' ),
            );
        }

        return $settings;
    }

    protected function isInstanceForEcommerce($locations)
    {
        if (empty($locations)) {
            return true;
        }

        $location = reset($locations);
        $locationType = $location->type;

        switch ($locationType) {
            case 'country':
                $country = $location->code;
                break;
            case 'state':
                $country = explode(':', $location->code)[0];
                break;
            default:
                $country = null;
                break;
        }

        return $country != 'CA';
    }

    protected function makeDropShippingAddressFields()
    {
        $addressFieldsOptions = [];
        $addressFields = FlagshipWoocommerceShipping::$dropShippingAddressFields;
        foreach ($addressFields as $key => $addressField) {
            $label = sprintf(__('Shipper %s','flagship-for-woocommerce'), $addressField);
            $addressFieldsOptions['dropshipping_address_'.$key] = array(
                'title' => __($label, 'flagship-for-woocommerce'),
                'type' => $key == 'state' ? 'select' : 'text',
                'options' => $this->getStates()
            );
        }
        return $addressFieldsOptions;
    }

    protected function getStates()
    {
        return [
            "AB" => "Alberta",
            "BC" => "British Columbia",
            "MB" => "Manitoba",
            "NB" => "New Brunswick",
            "NL" => "Newfoundland and Labrador",
            "NT" => "Northwest Territories",
            "NS" => "Nova Scotia",
            "NU" => "Nunavut",
            "ON" => "Ontario",
            "PE" => "Prince Edward Island",
            "QC" => "Quebec",
            "SK" => "Saskatchewan",
            "YT" => "Yukon",
        ];
    }

    protected function makeDisableCourierOptions($couriers, $isInternationalZone = false)
    {
        $disableCourierOptions = array();

        if (!$isInternationalZone) {
            unset($couriers['DHL']);
        }

        foreach ($couriers as $key => $value) {
            $settingName = 'disable_courier_'.$value;
            $settingLabel = sprintf(__('Disable %s rates', 'flagship-for-woocommerce'), $key);
            $disableCourierOptions[$settingName] = array(
                'title' => __($settingLabel, 'flagship-for-woocommerce'),
                'type' => 'checkbox',
                'default' => 'no',
            );
        }

        return $disableCourierOptions;
    }
}
