<?php
/**
 * Base test case.
 *
 * @category Tests
 */
class FlagshipShippingUnitTestCase extends WP_UnitTestCase
{
    protected $ctx = null;

    /**
     * Setup the test.
     */
    public function setUp()
    {
        parent::setUp();

        $settings = include __DIR__.'/../fixtures/FlagshipApplicationSettings.php';

        $this->ctx = Flagship_Application::get_instance();

        $this->ctx->load('Configs');
        $this->ctx['configs']->add($settings);

        $this->ctx->dependency(array(
            'Request',
            'Html',
            'View',
            'Options',
            'Client',
            'Notification',
            'Validation',
            // 'Hook',
            'Url',
            'Address',
        ));

        $this->ctx['options']->sync();
    }

    /**
     * Tear down the test.
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * load plugin fixture data into DB.
     */
    public static function setUpBeforeClass()
    {
        $settings = include_once __DIR__.'/../fixtures/FlagshipShippingPluginSettings.php';

        update_option('woocommerce_flagship_shipping_method_settings', $settings);
    }

    public function log($var)
    {
        $home = exec('echo ~');
        $text = $var;
        if (!is_string($var) && !is_array($var)) {
            ob_start();
            var_dump($var);
            $text = strip_tags(ob_get_clean());
        }
        if (is_array($var)) {
            $text = json_encode($var, JSON_PRETTY_PRINT);
        }
        file_put_contents($home.'/Desktop/data', date('Y-m-d H:i:s')."\t".print_r($text, 1)."\n", FILE_APPEND | LOCK_EX);
    }

    protected function setAccessProtectedMethod($object, $methodName)
    {
        $reflect = new ReflectionObject($object);

        $method = $reflect->getMethod($methodName);
        $method->setAccessible(true);

        return $method;
    }
}
