<?php
/**
 * flagship shipping test case.
 */
class FlagshipShippingConfirmationTest extends FlagshipShippingUnitTestCase
{
    protected $package;

    public function setUp()
    {
        parent::setUp();

        $this->order = FlagshipShippingWooCommerceFactory::createSimpleOrder();
        $this->flagshipQuoteRates = require __DIR__.'/../fixtures/FlagshipQuoteRates.php';

        $this->ctx->load('Package');
        $this->ctx->load('Order');
        $this->ctx['order']->initialize($this->order);

        $this->ctx->load('Confirmation');
    }

    public function testProtectedGetConfirmationRequest()
    {
        $method = $this->setAccessProtectedMethod($this->ctx['confirmation'], 'get_confirmation_request');
        $this->assertSame(array(
            'from' => array(
                'country' => 'CA',
                'state' => 'QC',
                'city' => 'POINTE-CLAIRE',
                'postal_code' => 'H9R5P9',
                'address' => '148 Brunswick',
                'name' => 'FlagShip WooCommerce Test App',
                'attn' => 'FlagShip Tester',
                'phone' => '+1 866 320 8383',
                'ext' => '',
            ),
            'to' => array(
                'name' => 'WooCompany',
                'attn' => 'Jeroen Sormani',
                'address' => 'WooAddress',
                'city' => 'WooCity',
                'state' => 'NY',
                'country' => 'US',
                'postal_code' => '123456',
                'phone' => '',
            ),
            'packages' => array(
                'items' => array(
                    0 => array(
                        'width' => 1,
                        'height' => 1,
                        'length' => 1,
                        'weight' => 4,
                        'description' => 'Flagship shipping package',
                    ),
                ),
                'units' => 'imperial',
                'type' => 'package',
            ),
            'payment' => array(
                'payer' => 'F',
            ),
            'service' => array(
                'courier_name' => 'purolator',
                'courier_code' => 'PurolatorExpress',
            ),
            'sold_to' => array(
                'sold_to_address' => array(
                    'name' => 'WooCompany',
                    'attn' => 'Jeroen Sormani',
                    'address' => 'WooAddress',
                    'city' => 'WooCity',
                    'state' => 'NY',
                    'country' => 'US',
                    'postal_code' => '123456',
                    'phone' => '',
                ),
                'duties_payer' => 'C',
                'reason_for_export' => 'P',
            ),
            'inquiry' => array(
                'company' => 'FlagShip WooCommerce Test App',
                'name' => 'FlagShip Tester',
                'inquiry_phone' => '18663208383',
            ),
            'declared_items' => array(
                'currency' => 'GBP',
                'ci_items' => array(
                    0 => array(
                        'product_name' => 'Dummy Product',
                        'description' => false,
                        'country_of_origin' => 'CA',
                        'quantity' => '4',
                        'unit_price' => '10',
                        'unit_weight' => 1,
                        'unit_of_measurement' => 'kilogram',
                    ),
                ),
            ),
        ), $method->invoke($this->ctx['confirmation'], $this->order));
    }
}
