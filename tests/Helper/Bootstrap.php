<?php

namespace FS\Test\Helper;

class Bootstrap
{
    protected $wpTestsDir = null;
    protected $testsDir = null;
    protected $pluginDir = null;

    public function __construct($testDir, $wpTestDir)
    {
        $this->testsDir = $testDir;
        $this->pluginDir = \dirname($this->testsDir);

        $this->wpTestsDir = $wpTestDir;

        // Give access to tests_add_filter() function.
        require_once $this->wpTestsDir.'/includes/functions.php';

        // Load WooCommerce
        \tests_add_filter('muplugins_loaded', array($this, 'loadWooCommerce'));

        // Load the plugin
        \tests_add_filter('muplugins_loaded', array($this, 'loadPlugin'));

        // Start up the WP testing environment.
        include_once $this->wpTestsDir.'/includes/bootstrap.php';    }

    public function loadPlugin()
    {
        require $this->pluginDir.'/flagship-for-woocommerce.php';
    }

    public function loadWooCommerce()
    {
        require_once $this->pluginDir.'/../woocommerce/woocommerce.php';
        require_once $this->pluginDir.'/../woocommerce/includes/admin/wc-admin-functions.php';
    }

    public static function initialize($testDir, $wpTestDir)
    {
        return new self($testDir, $wpTestDir);
    }
}
