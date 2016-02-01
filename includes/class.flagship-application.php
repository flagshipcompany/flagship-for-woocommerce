<?php

require_once FLS__PLUGIN_DIR.'includes/hook/class.flagship-api-hooks.php';
require_once FLS__PLUGIN_DIR.'includes/hook/class.flagship-filters.php';
require_once FLS__PLUGIN_DIR.'includes/hook/class.flagship-actions.php';
require_once FLS__PLUGIN_DIR.'includes/class.flagship-setup.php';

require_once FLS__PLUGIN_DIR.'includes/class.flagship-client.php';
require_once FLS__PLUGIN_DIR.'includes/class.flagship-notification.php';
require_once FLS__PLUGIN_DIR.'includes/class.flagship-validation.php';

require_once FLS__PLUGIN_DIR.'includes/class.flagship-view.php';
require_once FLS__PLUGIN_DIR.'includes/class.flagship-html.php';
require_once FLS__PLUGIN_DIR.'includes/class.flagship-request-formatter.php';
require_once FLS__PLUGIN_DIR.'includes/admin/meta-boxes/class.wc-meta-box-order-flagship-shipping-actions.php';

class Flagship_Application
{
    public static $_instance;
    public $text_domain;
    public $actions;
    public $filters;

    protected $api_client;
    protected $options;
    protected $notifications;
    public function __construct($options)
    {
        $this->options = $options;
        $this->api_client = new Flagship_Client($options['token']);
        $this->text_domain = 'flagship_shipping';
        $this->notification = new Flagship_Notification();
        $this->validation = new Flagship_Validation($this->api_client);
        $this->filters = new Flagship_Filters();
        $this->actions = new Flagship_Actions();
    }

    // instance methods
    //
    public function client($token = null)
    {
        if ($token) {
            $this->api_client->setToken($token);
        }

        return $this->api_client;
    }

    public function get_filters()
    {
        return $this->filters;
    }

    public function get_option($name, $default = null)
    {
        return isset($this->options[$name]) ? $this->options[$name] : $default;
    }

    // only check app settings, wordpress plugin activation is not considered here
    public function is_installed()
    {
        return false && $this->api_client->has_token() &&
            ($this->options['enabled'] == 'yes');
    }

    public function url_for($name, $escape = false)
    {
        $args = array();
        $base_url;

        switch ($name) {
            case 'flagship_shipping_settings':
                $args['page'] = wf_get_settings_url();
                $args['tab'] = 'shipping';
                $args['section'] = 'flagship_wc_shipping_method';
                $base_url = admin_url('admin.php');
                break;
            default:
                return false;
        }

        $url = add_query_arg($args, $base_url);

        if (!$escape) {
            return $url;
        }

        return esc_url($url);
    }

    public function integrity($options)
    {
        $screen = get_current_screen();

        console('integ:'.$screen->base);

        if ($screen->base == 'woocommerce_page_wc-settings') {
            $this->check_integrity($options);
            $this->notification->view();
        }

        return $options;
    }

    public function show_notifications()
    {
        $screen = get_current_screen();

        console('show_notif:'.$screen->base);

        if ($screen->base != 'woocommerce_page_wc-settings') {
            $this->check_integrity($options);
            $this->notification->view();
        }
    }

    public function check_integrity($options)
    {
        if (!$options['token']) {
            $this->notification->add('warning', esc_html__('Set your Flagship Shipping token.', 'flagship-shipping').' '.Flagship_Html::anchor('flagship_shipping_settings', 'click here', array('escape' => true)));
        }

        if (!$options['enabled'] || $options['enabled'] == 'no') {
            $this->notification->add('warning', esc_html__('Enable Flagship Shipping Method to get discounted shipping rates', 'flagship-shipping'));
        }
    }

    // static methods
    //
    public static function init(array $options = array(), $is_admin = false)
    {
        $flagship = self::get_instance($options);

        (new Flagship_Setup($flagship))->init($is_admin);

        return $flagship;
    }

    public static function get_instance($options = array())
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self($options);
        }

        return self::$_instance;
    }
}
