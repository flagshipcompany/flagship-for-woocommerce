<?php

namespace FS\Components\Shipping\Method;

use FS\Components\Event\ApplicationEvent;
use FS\Injection\I;
use FS\Components\Shipping\Object\Courier;
use FS\Components\Shipping\Request\Builder\PackageBoxBuilder;

class FlagShipWcShippingMethod extends \WC_Shipping_Method
{
    protected $ctx;
    protected $isLegacy = false;

    /**
     * Constructor for your shipping class.
     */
    public function __construct($instance_id = 0)
    {
        parent::__construct($instance_id);

        // FlagShip application context
        $this->ctx = \FS\Context\ApplicationContext::getInstance();

        $this->id = $this->ctx->_('\\FS\\Components\\Settings')['FLAGSHIP_SHIPPING_PLUGIN_ID'];
        $this->method_title = __('FlagShip Shipping', FLAGSHIP_SHIPPING_TEXT_DOMAIN);
        $this->method_description = __('Obtains real time shipping rates via FlagShip Shipping API', FLAGSHIP_SHIPPING_TEXT_DOMAIN);
        $this->supports = array(
            'shipping-zones',
            'instance-settings',
            'instance-settings-modal',
            'settings',
        );

        $this->title = __('FlagShip Shipping', FLAGSHIP_SHIPPING_TEXT_DOMAIN);

        $this->ctx
            ->_('\\FS\\Components\\Url');

        $this->ctx->alert('native');

        $options = $this->ctx->option();

        $options->sync($instance_id);

        $this->isLegacy = \version_compare(WC()->version, '2.6', '<');

        $this->init();

        $this->init_instance_settings();
    }

    /**
     * Init your settings.
     */
    public function init()
    {
        $formFields = array_merge($this->get_general_settings(), $this->get_instance_settings());
        $this->instance_form_fields = $formFields;
        $this->form_fields = $formFields;

        //flagship options
        $this->enabled = $this->get_instance_option('enabled');

        // Save settings in admin if you have any defined
        add_action('woocommerce_update_options_shipping_'.$this->id, array($this, 'process_admin_options'));

        load_plugin_textdomain(FLAGSHIP_SHIPPING_TEXT_DOMAIN, false, 'flagship-woocommerce-shipping/languages');
    }

    /**
     * Split the settings to general settings and shipping-zone-specific settings and display them respectively in those two settings pages.
     */
    public function get_admin_options_html()
    {
        if ($this->instance_id) {
            $settings_html = $this->generate_settings_html($this->get_instance_settings(), false);
        } else {
            $settings_html = $this->generate_settings_html($this->get_general_settings(), false);
        }

        return '<table class="form-table">'.$settings_html.'</table>';
    }

    /**
     * Override the WooCommerce process_admin_options() so that the saved settings from the global settings page will change the the general settings of each shipping zone.
     *
     * @return bool was anything saved?
     */
    public function process_admin_options()
    {
        if ($this->instance_id) {
            $this->init_instance_settings();
            $post_data = $this->get_post_data();

            if (!isset($post_data['instance_id'])) {
                return true;
            }

            foreach ($this->get_instance_settings() as $key => $field) {
                if ('title' !== $this->get_field_type($field)) {
                    try {
                        $this->instance_settings[ $key ] = $this->get_field_value($key, $field, $post_data);
                    } catch (Exception $e) {
                        $this->add_error($e->getMessage());
                    }
                }
            }

            return \update_option($this->get_instance_option_key(), apply_filters('woocommerce_shipping_'.$this->id.'_instance_settings_values', $this->instance_settings, $this));
        } else {
            $this->init_settings();
            $post_data = $this->get_post_data();
            $generalSettings = array();

            foreach ($this->get_general_settings() as $key => $field) {
                if ('title' !== $this->get_field_type($field)) {
                    try {
                        $this->settings[ $key ] = $this->get_field_value($key, $field, $post_data);
                        $generalSettings[ $key ] = $this->settings[ $key ];
                    } catch (Exception $e) {
                        $this->add_error($e->getMessage());
                    }
                }
            }

            $generalSettingsUpdated = \update_option($this->get_option_key(), apply_filters('woocommerce_settings_api_sanitized_fields_'.$this->id, $this->settings));
            $instanceSettingsOptions = I::get_all_instance_option_keys();
            $instanceSettingsUpdated = array();

            foreach ($instanceSettingsOptions as $key => $option) {
                $optionValue = \get_option($option);
                $updatedOptionValue = array_merge($optionValue, $generalSettings);
                $instanceSettingsUpdated[] = \update_option($option, apply_filters('woocommerce_shipping_'.$this->id.'_instance_settings_values', $updatedOptionValue, $this));
            }

            return $generalSettingsUpdated && !in_array(false, $instanceSettingsUpdated);
        }
    }

    /**
     * add notifications section on top of settings.
     */
    public function admin_options()
    {
        // request param
        $rp = $this->ctx->_('\\FS\\Components\\Web\\RequestParam');

        if (!$this->isLegacy && $rp->query->get('instance_id') == $this->instance_id) {
            $this->ctx->alert()->view();
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
        // use instance method's options
        $options = $this->ctx
            ->option()
            ->sync($this->instance_id);

        $event = new ApplicationEvent(ApplicationEvent::CALCULATE_SHIPPING);
        $event->setInputs(array(
            'package' => $package,
            'method' => $this,
        ));

        $this->ctx->publishEvent($event);
    }

    /**
     * render log type.
     */
    public function generate_log_html($key, $data)
    {
        $defaults = array(
            'title' => '',
            'disabled' => false,
            'class' => '',
            'css' => '',
            'placeholder' => '',
            'type' => 'log',
            'desc_tip' => false,
            'description' => '',
            'default' => [],
            'custom_attributes' => [],
        );

        ob_start();

        $this->ctx->render('option/log', [
            'field_key' => $this->get_field_key($key),
            'data' => \wp_parse_args($data, $defaults),
            'logs' => $this->get_instance_option($key, []),
            'description' => $this->get_description_html($data),
        ]);

        return ob_get_clean();
    }

    /**
     * Generate account details html.
     *
     * @return string
     */
    public function generate_package_box_html($key, $data)
    {
        ob_start();

        $packageBoxes = $this->instance_id ? $this->get_instance_option($key, []) : $this->get_option($key, []);
        $packageBoxes = PackageBoxBuilder::format_package_boxes($packageBoxes);

        $this->ctx->render('option/package-box', [
            'packageBoxes' => $packageBoxes,
        ]);

        return ob_get_clean();
    }

    public function generate_box_split_html($key, $data)
    {
        ob_start();

        $box_split_option = $this->get_option($key);
        $tooltip_html = \wc_help_tip($data['description']);

        //Options: value => label
        $options = array(
            'no' => __('Split by weight', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
            'yes' => __('Everything in one package box', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
            'each' => __('One box per item', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
            'packing' => __('Use FlagShip Packing API to pack items into boxes', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
        );

        $this->ctx->render('option/box-split', [
            'box_split_option' => $box_split_option,
            'options' => $options,
            'tooltip_html' => $tooltip_html,
            'field_name' => 'woocommerce_'.$this->ctx->setting('FLAGSHIP_SHIPPING_PLUGIN_ID').'_'.$key,
            'split_weight_field_name' => 'woocommerce_'.$this->ctx->setting('FLAGSHIP_SHIPPING_PLUGIN_ID').'_default_package_box_split_weight',
            'packing_box_field_name' => 'woocommerce_'.$this->ctx->setting('FLAGSHIP_SHIPPING_PLUGIN_ID').'_package_box',
        ]);

        return ob_get_clean();
    }

    protected function get_general_settings()
    {
        return array(
            'basics' => array(
                'title' => __('Essentials', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'title',
                'id' => 'flagship_shipping_basics',
            ),
            'enabled' => array(
                'title' => __('FlagShip Shipping', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'checkbox',
                'label' => __('Enable this shipping method', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'default' => 'no',
            ),
            'title' => array(
                'title' => __('Method Title', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'text',
                'description' => __('This controls the name of the shipping service during checkout.', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'default' => __('FlagShip Shipping', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'desc_tip' => true,
            ),
            'token' => array(
                'title' => __('FlagShip Access Token', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'text',
                'description' => __('After <a href="https://www.flagshipcompany.com/sign-up/">signup</a>, get a <a target="_blank" href="https://auth.smartship.io/tokens/">access token here</a>.', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'default' => '',
                'custom_attributes' => array(
                    'maxlength' => 255,
                ),
            ),
            'autocomplete_order' => array(
                'title' => __('Auto Complete "Processing" Orders', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'label' => __( 'Auto complete "Processing" orders when Flagship shipment is confirmed', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'checkbox',
                'description' => __('If enabled, "Processing" order will be automatically set to "Completed" when Flagship Shipment is confirmed', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'default' => 'no'
            ),
            'shipper_criteria' => array(
                'title' => __('Shipper Information', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'title',
                'id' => 'flagship_shipping_criteria',
                'description' => __('Shipper information which allows getting live rates, create shipment, schedule pick-up, etc.', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
            ),
            'origin' => array(
                'title' => __('Shipper Postal Code', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'text',
                'description' => __('Enter a valid <strong>Canadian</strong> postal code for the <strong>Shipper</strong>.', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
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
                    'NL' => __('Newfoundland and Labrador', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                    'NT' => __('Northwest Territories', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                    'NS' => __('Nova Scotia', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                    'NU' => __('Nunavut', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                    'ON' => __('Ontario', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                    'PE' => __('Prince Edward Island', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                    'QC' => __('Quebec', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                    'SK' => __('Saskatchewan', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
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
            'freight_shipper_suite' => array(
                'title' => __('Shipper Suite Number', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'text',
                'default' => '',
                'description' => __('Optional, if applicable', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
            ),
            'receiver_residential' => array(
                'title' => __('Residential', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'label' => __('Receiver Address is Residential?', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'checkbox',
                'default' => 'no',
            ),
            // 'flagship_insurance' => array(
            //     'title' => __('Insurance', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
            //     'label' => __('Add Insurance', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
            //     'type' => 'checkbox',
            //     'default' => 'no',
            // ),
            'shipping_pickup' => array(
                'title' => __('Pickup', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'title',
                'description' => __('schedule pick-up for your shipment. Don\'t forget to attach labels!', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'id' => 'flagship_shipping_pickup',
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
                'type' => 'email',
                'default' => get_option('admin_email'),
            ),
            'shipping_packaging' => array(
                'title' => __('Parcel / Packaging', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'title',
                'description' => __('How to split your items into boxes', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'id' => 'flagship_shipping_packaging',
            ),
            'default_package_box_split' => array(
                'title' => __('Box Split', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'box_split',
                'description' => __('If the option FlagShip packing API is chosen, you will have to provide at least one package box with dimensions. It will also ignore all settings from the normal weight driven packing method.', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'default' => 'no',
                'desc_tip' => true,
            ),
            'default_package_box_split_weight' => array(
                'title' => __('Box Split Weight', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'description' => __('Maximun weight per each package box (lbs)', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'css' => 'width:70px;',
                'desc_tip' => true,
                'default' => 20,
                'type' => 'number',
                'custom_attributes' => array(
                    'min' => 0,
                    'step' => 1,
                ),
            ),
            'package_box' => array(
                'type' => 'package_box',
            ),
            'tracking_configs' => array(
                'title' => __('Tracking', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'title',
                'id' => 'flagship_tracking_configs',
            ),
            'tracking_emails' => array(
                'title' => __('Tracking Emails', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'text',
                'description' => __('(optional) If provided, the email address(s) will always receive tracking notifications. Emails should be semicolon separated (;).', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'custom_attributes' => array(
                    'maxlength' => 100,
                ),
            ),
            'add_billing_email_to_tracking' => array(
                'title' => __('Send tracking updates to billing email', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'checkbox',
                'default' => 'no',
            ),
            'shipping_configs' => array(
                'title' => 'Configuration',
                'type' => 'title',
                'id' => 'flagship_shipping_configs',
            ),
            'disable_api_warning' => array(
                'title' => __('Disable Cart/Checkout API warning', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'checkbox',
                'default' => 'no',
                'description' => __('Once disabled, FlagShip will store warnings under following option "Cart/Checkout API warning logs"', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
            ),
            'api_warning_log' => array(
                'title' => '',
                'type' => 'log',
                'description' => __('Cart/Checkout API warning logs (10 latest)', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'desc_tip' => __('Cart/Checkout API warning logs (10 latest)', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
            ),
        );
    }

    protected function get_instance_settings()
    {
        $instanceSettings = array(
            'shipping_rates_configs' => array(
                'title' => 'Options',
                'type' => 'title',
                'id' => 'flagship_shipping_configs',
            ),
            'allow_standard_rates' => array(
                'title' => __('Offer Standard Rates', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'checkbox',
                'default' => 'yes',
                'checkboxgroup' => 'start',
            ),
            'allow_express_rates' => array(
                'title' => __('Offer Express Rates', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'checkbox',
                'default' => 'yes',
            ),
            'allow_overnight_rates' => array(
                'title' => __('Offer Overnight Rates', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'checkbox',
                'default' => 'yes',
                'checkboxgroup' => 'end',
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
            'allow_fake_cart_rate_discount' => array(
                'title' => __('Show fake rate discount in cart/checkout', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'checkbox',
                'default' => 'no',
                'checkboxgroup' => 'end',
            ),
            'fake_cart_rate_discount' => array(
                'title' => __('Fake rate discount (%)', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'text',
                'description' => __('For instance, 35 stands for 35%', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'default' => '35',
            ),
            'shipping_taxation' => array(
                'title' => __('Tax', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'title',
                'id' => 'flagship_shipping_taxation',
            ),
            'apply_tax_by_flagship' => array(
                'title' => __('Calculate tax', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'label' => __('Click here to include taxes in the price. Only use this if WooCommerce is not applying taxes to your cart', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'checkbox',
                'description' => __('If you have taxes enabled, make sure you don’t click this box or you will double tax the shipping fees.', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'default' => 'no',
            ),
            'shipping_markup' => array(
                'title' => __('Markup', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'title',
                'description' => __('Store owner may apply additional fee for shipping.', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'id' => 'flagship_shipping_markup',
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
                'type' => 'decimal',
                'default' => 0,
            ),
            'signature_required' => array(
                'title' => __('Signature required on delivery', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'description' => __('If checked, all the shipments to this shipping zone will be signature required on delivery', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'checkbox',
                'default' => 'no',
            ),
            'show_transit_time' => array(
                'title' => __('Show transit time in shopping cart', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'description' => __('If checked, the transit times of couriers will be shown', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'checkbox',
                'default' => 'no',
            ),
        );

        $disableCourierOptions = $this->makeDisableCourierOptions();

        return array_merge($instanceSettings, $disableCourierOptions);
    }

    protected function makeDisableCourierOptions()
    {
        $disableCourierOptions = array();

        foreach (Courier::$couriers as $key => $value) {
            $settingName = 'disable_courier_'.$value;
            $settingLabel = sprintf('Disable %s Rates', $key);
            $disableCourierOptions[$settingName] = array(
                'title' => __($settingLabel, FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'checkbox',
                'default' => 'no',
                'checkboxgroup' => 'start',
            );
        }

        return $disableCourierOptions;
    }
}
