<?php

namespace FS\Components\Shipping\Methods;

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

        $this->id = $this->ctx->getComponent('\\FS\\Components\\Settings')['FLAGSHIP_SHIPPING_PLUGIN_ID'];
        $this->method_title = __('FlagShip Shipping', FLAGSHIP_SHIPPING_TEXT_DOMAIN);
        $this->method_description = __('Obtains real time shipping rates via FlagShip Shipping API', FLAGSHIP_SHIPPING_TEXT_DOMAIN);
        $this->supports = array(
            'shipping-zones',
            'instance-settings',
            'instance-settings-modal',
            'settings',
        );

        $this->title = __('FlagShip Shipping', FLAGSHIP_SHIPPING_TEXT_DOMAIN);

        // flagship options
        $this->enabled = $this->get_instance_option('enabled');

        // load components
        $this->ctx
            ->getComponent('\\FS\\Components\\Hook\\HookManager')
            ->registerHook('\\FS\\Components\\Hook\\SettingsFilters');

        $this->ctx
            ->getComponent('\\FS\\Components\\Shipping\\Command');

        $this->ctx
            ->getComponent('\\FS\\Components\\Url');

        $this->ctx
            ->getComponent('\\FS\\Components\\Options')
            ->sync($this->instance_id);

        $this->isLegacy = \version_compare(WC()->version, '2.6', '<');

        $this->init_instance_settings();

        $this->init();
    }

    /**
     * Init your settings.
     */
    public function init()
    {
        $formFields = array(
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
            'shipping_packaging' => array(
                'title' => __('Parcel / Packaging', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'title',
                'description' => __('How to split your items into boxes', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'id' => 'flagship_shipping_packaging',
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
                'css' => 'width:70px;',
                'desc_tip' => true,
                'default' => 20,
                'type' => 'number',
                'custom_attributes' => array(
                    'min' => 0,
                    'step' => 1,
                ),
            ),
            'shipping_taxation' => array(
                'title' => __('Tax', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'title',
                'id' => 'flagship_shipping_markup',
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
            'shipping_configs' => array(
                'title' => 'Configuration',
                'type' => 'title',
                'id' => 'flagship_shipping_configs',
            ),
            'disable_courier_fedex' => array(
                'title' => __('Disable FedEx Rates', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                'type' => 'checkbox',
                'default' => 'no',
                'checkboxgroup' => 'start',
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
                'checkboxgroup' => 'end',
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
            ),
        );

        $this->instance_form_fields = $formFields;
        $this->form_fields = $formFields;

        // Save settings in admin if you have any defined
        add_action('woocommerce_update_options_shipping_'.$this->id, array($this, 'process_admin_options'));

        load_plugin_textdomain(FLAGSHIP_SHIPPING_TEXT_DOMAIN, false, 'flagship-for-woocommerce/languages');
    }

    /**
     * add notifications section on top of settings.
     */
    public function admin_options()
    {
        // request param
        $rp = $this->ctx->getComponent('\\FS\\Components\\Web\\RequestParam');

        if (!$this->isLegacy && $rp->query->get('instance_id') == $this->instance_id) {
            $this->ctx->getComponent('\\FS\\Components\\Notifier')->view();
        }

        parent::admin_options();
    }

    /**
     * we need to reinitialize the settings field data.
     * 
     * @return bool
     */
    public function process_admin_options()
    {
        $success = parent::process_admin_options();

        $this->init_instance_settings();

        return $success;
    }

    /**
     * calculate_shipping function.
     *
     * @param array $package
     */
    public function calculate_shipping($package = array())
    {
        $options = $this->ctx->getComponent('\\FS\\Components\\Options');
        $options->sync($this->instance_id);

        $command = $this->ctx->getComponent('\\FS\\Components\\Shipping\\Command');

        $factory = $this->ctx->getComponent('\\FS\\Components\\Shipping\\Factory\\ShoppingCartRateRequestFactory');

        $client = $this->ctx->getComponent('\\FS\\Components\\Http\\Client');
        $client->setToken($options->get('token'));

        $notifier = $this->ctx->getComponent('\\FS\\Components\\Notifier');

        $rateProcessor = $this->ctx->getComponent('\\FS\\Components\\Shipping\\RateProcessor');
        $notifier->scope('cart');

        // when store owner disable front end warning for their customer
        if ($options->equal('disable_api_warning', 'yes')) {
            $notifier->enableSilentLogging();
        }

        // no shipping address, alert customer
        if (empty($package['destination']['postcode'])) {
            $notifier->notice('Add shipping address to get shipping rates! (click "Calculate Shipping")');
            $notifier->view();

            return;
        }

        $response = $command->quote(
            $client,
            $factory->setPayload(array(
                'package' => $package,
                'options' => $options,
            ))->getRequest()
        );

        if (!$response->isSuccessful()) {
            $notifier->error('Flagship Shipping has some difficulty in retrieving the rates. Please contact site administrator for assistance.<br/>');
            $notifier->view();

            return;
        }

        $rates = $rateProcessor->convertToWcShippingRate($response->getBody(), $this->instance_id);

        $offer_rates = $this->get_instance_option('offer_rates', 'all');

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

        $notifier->view();
    }

    /**
     * render log type.
     */
    public function generate_log_html($key, $data)
    {
        $field_key = $this->get_field_key($key);

        $defaults = array(
            'title' => '',
            'disabled' => false,
            'class' => '',
            'css' => '',
            'placeholder' => '',
            'type' => 'log',
            'desc_tip' => false,
            'description' => '',
            'default' => array(),
            'custom_attributes' => array(),
        );

        $data = wp_parse_args($data, $defaults);
        $logs = $this->get_instance_option($key, array());

        ob_start();
        ?>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <?php echo wp_kses_post($data['title']);
        ?>
            </th>
            <td class="forminp">
                <input type="hidden" 
                    id="<?php echo esc_attr($field_key);
        ?>"
                    name="<?php echo esc_attr($field_key);
        ?>"
                    value=""
                />
        <?php if ($logs) : ?>
                <table class="wc_gateways widefat" cellspacing="0">
                    <thead>
                        <tr>
                            <th><?php _e('Timestamp', FLAGSHIP_SHIPPING_TEXT_DOMAIN) ?></th>
                            <th><?php _e('Log', FLAGSHIP_SHIPPING_TEXT_DOMAIN) ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log) :?>
                        <tr>
                            <td width="20%"><?php echo date('Y-m-d H:i:s', $log['timestamp']);
        ?></td>
                            <td><?php $this->ctx['html']->ul_e($log['log']);
        ?></td>
                        </tr>
                        <?php endforeach;
        ?>
                    </tbody>
                </table>
                <?php echo $this->get_description_html($data);
        ?>
        <?php endif;
        ?>
            </td>
        </tr>
        <?php

        return ob_get_clean();
    }
}
