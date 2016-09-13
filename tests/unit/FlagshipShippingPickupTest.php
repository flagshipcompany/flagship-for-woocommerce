<?php
/**
 * flagship shipping test case.
 */
class FlagshipShippingPickupTest extends FlagshipShippingUnitTestCase
{
    protected $shipping;

    public function setUp()
    {
        parent::setUp();

        $this->order = FlagshipShippingWooCommerceFactory::createSimpleOrder();
        $this->shipping = array(
            'order' => $this->order,
            'shipment' => array(
                'shipment_id' => 1517011,
                'tracking_number' => '329020072602',
                'price' => array(
                    'charges' => array(
                        'freight' => 6.69,
                        'residential_surcharge' => 3.7,
                        'fuel_surcharge' => 0.4,
                    ),
                    'subtotal' => 10.79,
                    'total' => 12.41,
                    'taxes' => array(
                        'gst' => 0.54,
                        'qst' => 1.08,
                    ),
                ),
                'service' => array(
                    'flagship_code' => 'express',
                    'courier_code' => 'PurolatorExpress',
                    'courier_desc' => 'Purolator Express',
                    'courier_name' => 'Purolator',
                    'transit_time' => 1,
                    'estimated_delivery_date' => '2016-09-14',
                ),
                'labels' => array(
                    'regular' => "https:\/\/s3.amazonaws.com\/waybills\/201609\/10e1e80cddbfd3f1f213d364ed7a2c0c\/reg_329020072602.pdf",
                    'thermal' => "https:\/\/s3.amazonaws.com\/waybills\/201609\/10e1e80cddbfd3f1f213d364ed7a2c0c\/therm_329020072602.pdf",
                ),
                'packages' => array(
                    array(
                        'width' => '1',
                        'height' => '1',
                        'length' => '1',
                        'weight' => '1',
                        'description' => 'Flagship shipping package',
                        'pin' => '329020072602',
                    ),
                ),
            ),
            'shipment_id' => '1517011',
            'date' => date('Y-m-d'),
        );

        $this->flagshipQuoteRates = require __DIR__.'/../fixtures/FlagshipQuoteRates.php';

        $this->ctx->load('Package');
        $this->ctx->load('Order');
        $this->ctx['order']->initialize($this->order);

        $this->ctx->load('Pickup');
    }

    public function testProtectedGetConfirmationRequest()
    {
        $this->assertSame(array(
            'address' => array(
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
            'courier' => 'purolator',
            'boxes' => 1,
            'weight' => 1,
            'date' => date('Y-m-d'),
            'from' => '09:00',
            'until' => '17:00',
            'units' => 'imperial',
            'location' => 'Reception',
            'to_country' => 'US',
            'is_ground' => false,
        ), $this->ctx['pickup']->get_single_pickup_schedule_request($this->shipping));
    }
}
