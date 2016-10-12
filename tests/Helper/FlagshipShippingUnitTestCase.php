<?php

namespace FS\Test\Helper;

/**
 * Base test case.
 *
 * @category Tests
 */
class FlagshipShippingUnitTestCase extends \WP_UnitTestCase
{
    protected $ctx = null;

    /**
     * Setup the test.
     */
    public function setUp()
    {
        parent::setUp();

        $settings = include __DIR__.'/../Fixture/FlagshipApplicationSettings.php';

        // $ctx = \FS\Context\ApplicationContext::getInstance();

        // $ctx->setContainer(new \FS\Container());
        // $ctx->setConfiguration(new \FS\Configurations\WordPress\Configuration());

        // $ctx->getComponents(array(
        //     '\\FS\\Components\\Web\\RequestParam',
        //     '\\FS\\Components\\Settings',
        //     '\\FS\\Components\\Options',
        //     '\\FS\\Components\\Debugger',
        //     '\\FS\\Components\\Html',
        //     '\\FS\\Components\\Viewer',
        //     '\\FS\\Components\\Url',
        //     '\\FS\\Components\\Notifier',
        //     '\\FS\\Components\\Hook\\HookManager',
        //     '\\FS\\Components\\Http\\Client',
        // ));

        $this->ctx = \FS\Context\ApplicationContext::initialize(
            new \FS\Container(),
            new \FS\Configurations\WordPress\Configuration()
        );
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
        $settings = include_once __DIR__.'/../Fixture/FlagshipShippingPluginSettings.php';

        update_option('woocommerce_flagship_shipping_method_settings', $settings);
    }

    public function getApplicationContext()
    {
        return $this->ctx;
    }

    protected function setAccessProtectedMethod($object, $methodName)
    {
        $reflect = new ReflectionObject($object);

        $method = $reflect->getMethod($methodName);
        $method->setAccessible(true);

        return $method;
    }
}
