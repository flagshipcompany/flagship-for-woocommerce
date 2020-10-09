<?php

class Flagship_Woocommerce_Shipping_Method_Tests_Bootstrap
{
    public $wpTestsDir  = null;
    public $testsDir    = null;
    public $pluginDir   = null;

    /**
     * Constructor to the bootstrap file
     */
    public function __construct()
    {
        if (!isset($_SERVER['SERVER_NAME'])) {
            $_SERVER['SERVER_NAME'] = 'localhost';
        }

        if (getenv('WP_MULTISITE')) {
            define('WP_TESTS_MULTISITE', 1);
        }

        $this->testsDir = dirname(__FILE__);
        $this->pluginDir = dirname($this->testsDir);
        $this->wpTestsDir = getenv('WP_TESTS_DIR')
            ? getenv('WP_TESTS_DIR')
            : '/tmp/wordpress-tests-lib';

        // Load test functions
        include_once $this->wpTestsDir . '/includes/functions.php';

        // Load WooCommerce
        tests_add_filter('muplugins_loaded', array($this, 'loadWooCommerce'));

        // Load the plugin
        tests_add_filter('muplugins_loaded', array($this, 'loadPlugin'));

        // Start up the WP testing environment.
        include_once $this->wpTestsDir . '/includes/bootstrap.php';

        $this->includes();
    }

    /**
     * Loads the plugin for the current PHPUnit runtime
     *
     * @return void
     */
    public function loadPlugin()
    {
        include_once $this->pluginDir . '/flagship-woocommerce-shipping-method.php';
    }

    /**
     * Loads the WooCommerce plugin for the current PHPUnit runtime
     *
     * @return void
     */
    public function loadWooCommerce()
    {
        include_once $this->pluginDir . '/../woocommerce/woocommerce.php';
        include_once $this->pluginDir . '/../woocommerce/includes/admin/wc-admin-functions.php';
    }

    public function includes()
    {
        include_once $this->testsDir . '/utility/FlagshipShippingUnitTestCase.php';
    }
}

new Flagship_Woocommerce_Shipping_Method_Tests_Bootstrap();