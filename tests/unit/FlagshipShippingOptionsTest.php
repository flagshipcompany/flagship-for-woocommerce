<?php
/**
 * Class SampleTest.
 */

/**
 * flagship shipping test case.
 */
class FlagshipShippingOptionsTest extends FlagshipShippingUnitTestCase
{
    public function testOptionsGet()
    {
        $this->assertEquals($this->ctx['options']->get('enabled'), 'yes');
    }

    public function testOptionsEqual()
    {
        $this->assertTrue($this->ctx['options']->equal('enabled', 'yes'));
    }

    public function testOptionsNotEqual()
    {
        $this->assertTrue($this->ctx['options']->not_equal('enabled', 'no'));
    }

    public function testOptionsSet()
    {
        $title = 'FlagShip Awesome!';

        $this->ctx['options']->set('title', $title);
        $this->assertEquals($this->ctx['options']->get('title'), $title);
    }

    public function testOptionsSync()
    {
        // change options value to a new one, then reload fixtures
        $title = 'FlagShip Awesome!';
        $this->ctx['options']->set('title', $title);

        self::setUpBeforeClass();

        $this->ctx['options']->sync();
        $this->assertNotEquals($this->ctx['options']->get('title'), $title);
    }

    public function testOptionsApiLog()
    {
        $this->assertEmpty($this->ctx['options']->get('api_warning_log'));

        $this->ctx['options']->log('test warning message');

        $this->assertNotEmpty($this->ctx['options']->get('api_warning_log'));
    }
}
