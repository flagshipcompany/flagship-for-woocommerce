<?php
/**
 * flagship shipping test case.
 */
class FlagshipShippingPackageTest extends FlagshipShippingUnitTestCase
{
    protected $package;

    public function setUp()
    {
        parent::setUp();

        $this->package = require __DIR__.'/../fixtures/Package.php';
        $this->order = FlagshipShippingWooCommerceFactory::createSimpleOrder();

        foreach ($this->package['contents'] as $key => $package) {
            $this->package['contents'][$key]['data'] = FlagshipShippingWooCommerceFactory::createSimpleProduct();
        }

        $this->ctx->load('Package');
    }

    public function testPackageGetQuote()
    {
        $this->assertSame(array(
            'items' => array(
                array(
                    'width' => 1,
                    'height' => 1,
                    'length' => 1,
                    'weight' => 2,
                    'description' => 'Flagship shipping package',
                ),
            ),
            'units' => 'imperial',
            'type' => 'package',
        ), $this->ctx['package']->get_quote($this->package));
    }

    public function testPackageGetOrder()
    {
        $this->assertSame(array(
            'items' => array(
                array(
                    'width' => 1,
                    'height' => 1,
                    'length' => 1,
                    'weight' => 4,
                    'description' => 'Flagship shipping package',
                ),
            ),
            'units' => 'imperial',
            'type' => 'package',
        ), $this->ctx['package']->get_order($this->order));
    }
}
