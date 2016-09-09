<?php
/**
 * Class SampleTest.
 */

/**
 * flagship shipping test case.
 */
class Flagship_Shipping_Test extends Flagship_UnitTestCase
{
    public function test_options_sync()
    {
        $this->ctx['options']->sync();

        $this->assertEquals($this->ctx['options']->get('enabled'), 'yes');
        // $this->assertTrue(in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))));
    }
}
