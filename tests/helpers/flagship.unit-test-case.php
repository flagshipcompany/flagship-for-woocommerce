<?php
/**
 * Base test case.
 *
 * @category Tests
 */
class Flagship_UnitTestCase extends WP_UnitTestCase
{
    protected $ctx = null;

    /**
     * Setup the test.
     */
    public function setUp()
    {
        parent::setUp();

        $flagship_application_settings = include __DIR__.'/../data/flagship_application_settings.php';

        $this->ctx = Flagship_Application::get_instance();

        $this->ctx->load('Configs');
        $this->ctx['configs']->add($flagship_application_settings);

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
}
