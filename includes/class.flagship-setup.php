<?php

/**
 * Set up Flagship WooCommerce plugin.
 */
class Flagship_Setup
{
    protected $flagship;
    protected $isAdmin = false;

    public function __construct(Flagship_Application $flagship)
    {
        $this->flagship = $flagship;
    }

    public function init($is_admin = false)
    {
        // register plugin method
        add_action('woocommerce_shipping_init', array('Flagship_Setup', 'flagship_wc_shipping_method_init'));
        add_filter('woocommerce_shipping_methods', array('Flagship_Setup', 'add_flagship_wc_shipping_method'));

        if (!$is_admin) {
            return;
        }

        // add setting link to plugin page
        add_filter('plugin_action_links_'.FLS__PLUGIN_BASENAME, array($this, 'plugin_action_links'), 10, 2);
        // check for flagship app integrity
        // eg: token is set or not, etc.
        if (!$this->flagship->is_installed()) {
            add_action('admin_notices', array($this->flagship, 'warning_installation'));
        }
    }

    // static methods
    public static function flagship_wc_shipping_method_init()
    {
        if (!class_exists('Flagship_WC_Shipping_Method')) {
            include_once FLS__PLUGIN_DIR.'includes/class.flagship-wc-shipping-method.php';
        }
    }

    public static function add_flagship_wc_shipping_method($methods)
    {
        $methods[] = 'Flagship_WC_Shipping_Method';

        return $methods;
    }

    // instance methods
    //
    public function plugin_action_links($links, $file)
    {
        if ($file == FLS__PLUGIN_BASENAME) {
            array_unshift($links, Flagship_Html::anchor('flagship_shipping_settings', 'Settings', array(
                'escape' => true,
                'target' => true,
            )));
        }

        return $links;
    }
}
