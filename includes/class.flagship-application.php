<?php

require_once FLS__PLUGIN_DIR.'includes/class.flagship-setup.php';
require_once FLS__PLUGIN_DIR.'includes/class.flagship-client.php';
require_once FLS__PLUGIN_DIR.'includes/class.flagship-view.php';
require_once FLS__PLUGIN_DIR.'includes/class.flagship-html.php';

class Flagship_Application
{
    public static $_instance;
    public $taxt_domain;

    protected $api_client;
    protected $html_helper;
    protected $options;

    public function __construct($options)
    {
        $this->options = $options;
        $this->api_client = new Flagship_Client($options['token']);
        $this->text_domain = 'flagship_shipping';
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

    // only check app settings, wordpress plugin activation is not considered here
    public function is_installed()
    {
        return $this->api_client->has_token() &&
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

    public function notice($args, $page = null)
    {
        $args['app'] = $this;

        Flagship_View::notice($args);
    }

    // warnings
    //
    public function warning_installation()
    {
        // only show installation warning for plugins page
        global $hook_suffix;

        if ($hook_suffix == 'plugins.php') {
            $this->notice(array('type' => 'token', 'app' => $this));
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
