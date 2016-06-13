<?php

require_once FLS__PLUGIN_DIR.'includes/hook/class.flagship-api-hooks.php';

require_once FLS__PLUGIN_DIR.'includes/class.flagship-view.php';
require_once FLS__PLUGIN_DIR.'includes/class.flagship-html.php';
require_once FLS__PLUGIN_DIR.'includes/class.flagship-request-formatter.php';

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
        $this->text_domain = 'flagship_shipping';

        $this->options = $options;

        $this->client = self::factory('Client')->set_token($options['token']);
        $this->notification = self::factory('Notification');
        $this->validation = self::factory('Validation')->set_client($this->client);
        $this->hooks = self::factory('Hook_Manager', 'hook');
    }

    // instance methods
    //
    public function client($token = null)
    {
        if ($token) {
            $this->client->set_token($token);
        }

        return $this->client;
    }

    public function get_option($name, $default = null)
    {
        return isset($this->options[$name]) ? $this->options[$name] : $default;
    }

    // only check app settings, wordpress plugin activation is not considered here
    public function is_installed()
    {
        return false && $this->client->has_token() &&
            ($this->options['enabled'] == 'yes');
    }

    public function url_for($name, $escape = false)
    {
        $args = array();
        $base_url;

        switch ($name) {
            case 'flagship_shipping_settings':
                $args['page'] = version_compare(WC()->version, '2.1', '>=') ? 'wc-settings' : 'woocommerce_settings';
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

    // static methods
    //
    public static function factory($className, $type = 'flagship')
    {
        $class = str_replace('_', '-', strtolower($className));

        $filePath = FLS__PLUGIN_DIR.'includes/';
        $realClassName = '';

        switch ($type) {
            case 'flagship':
                $filePath .= 'class.flagship-'.$class.'.php';
                $realClassName = 'Flagship_'.$className;
                break;
            case 'hook':
                $filePath .= 'hook/class.flagship-'.$class.'.php';
                $realClassName = 'Flagship_'.$className;
                break;
        }

        if (!file_exists($filePath) || !$realClassName) {
            throw new Exception($className.' does not exist.');
        }

        require_once $filePath;

        return new $realClassName();
    }

    public static function init(array $options = array(), $is_admin = false)
    {
        $flagship = self::get_instance($options);

        $setup = self::factory('Setup');

        $setup->set_application($flagship);
        $setup->init($is_admin);

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
