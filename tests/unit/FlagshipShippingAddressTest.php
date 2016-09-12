<?php
/**
 * flagship shipping test case.
 */
class FlagshipShippingAddressTest extends FlagshipShippingUnitTestCase
{
    protected $package;

    public function setUp()
    {
        $this->package = require __DIR__.'/../fixtures/Package.php';

        parent::setUp();
    }

    public function testAddressGetFrom()
    {
        $this->assertSame(array(
            'country' => 'CA',
            'state' => 'QC',
            'city' => 'POINTE-CLAIRE',
            'postal_code' => 'H9R5P9',
            'address' => '148 Brunswick',
            'name' => 'FlagShip WooCommerce Test App',
            'attn' => 'FlagShip Tester',
            'phone' => '+1 866 320 8383',
            'ext' => '',
        ), $this->ctx['address']->get_from());
    }

    public function testAddressGetQuoteTo()
    {
        $this->assertSame(array(
            'country' => 'CA',
            'state' => 'QC',
            'city' => 'Verdun',
            'postal_code' => 'H3E 1H2',
            'address' => '1460 N. MAIN STREET, # 9 ',
        ), $this->ctx['address']->get_quote_to($this->package));
    }

    public function testAddressHasReceiverAddress()
    {
        $this->assertTrue($this->ctx['address']->has_receiver_address($this->package));
    }

    public function testAddressGetOrderTo()
    {
        $order = FlagshipShippingWooCommerceFactory::createSimpleOrder();

        $this->log($order);

        $this->assertSame(array(
            'name' => 'WooCompany',
            'attn' => 'Jeroen Sormani',
            'address' => 'WooAddress',
            'city' => 'WooCity',
            'state' => 'NY',
            'country' => 'US',
            'postal_code' => '123456',
            'phone' => '',
        ), $this->ctx['address']->get_order_to($order));
    }
}
