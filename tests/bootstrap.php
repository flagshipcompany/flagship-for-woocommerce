<?php
/**
 * PHPUnit bootstrap file.
 *
 * @category Tests
 *
 * @author   ryan <yi-cheng@flagshipcompany.com>
 *
 * @link https://github.com/flagshipcompany/flagship-for-woocommerce
 */
class Flagship_For_WooCommerce_Tests_Bootstrap
{
    protected $wpTestsDir = null;
    protected $testsDir = null;
    protected $pluginDir = null;

    public function __construct()
    {
        $this->testsDir = dirname(__FILE__);
        $this->pluginDir = dirname($this->testsDir);

        $this->wpTestsDir = getenv('WP_TESTS_DIR') ? getenv('WP_TESTS_DIR') : '/tmp/wordpress-tests-lib';

        // Give access to tests_add_filter() function.
        require_once $this->wpTestsDir.'/includes/functions.php';

        // Load WooCommerce
        tests_add_filter('muplugins_loaded', array($this, 'loadWooCommerce'));

        // Load the plugin
        tests_add_filter('muplugins_loaded', array($this, 'loadPlugin'));

        // Start up the WP testing environment.
        include_once $this->wpTestsDir.'/includes/bootstrap.php';

        $this->dependency();
    }

    public function loadPlugin()
    {
        require $this->pluginDir.'/flagship-for-woocommerce.php';
        require_once $this->pluginDir.'/includes/FSApplicationContext.php';
    }

    public function loadWooCommerce()
    {
        require_once $this->pluginDir.'/../woocommerce/woocommerce.php';
        require_once $this->pluginDir.'/../woocommerce/includes/admin/wc-admin-functions.php';
    }

    public function dependency()
    {
        require_once $this->testsDir.'/helpers/FlagshipShippingUnitTestCase.php';
        require_once $this->testsDir.'/helpers/FlagshipShippingWooCommerceFactory.php';
    }
}

new Flagship_For_WooCommerce_Tests_Bootstrap();
