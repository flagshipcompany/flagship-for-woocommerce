<?php

namespace FS\Shipping\RequestFactory;

class RequestFactoryTestCase extends \FS\Test\Helper\FlagshipShippingUnitTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->package = require __DIR__.'/../../Fixture/Package.php';
        $this->order = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Order\\ShoppingOrder')
            ->setWcOrder(\FS\Test\Helper\FlagshipShippingWooCommerceFactory::createSimpleOrder());

        $this->order->setFlagShipRaw(array(
            'shipment_id' => 1517028,
            'tracking_number' => '794631711299',
            'price' => array(
                'charges' => array(
                    'freight' => 18.9,
                    'fuel_surcharge' => 1.27,
                ),
                'subtotal' => 20.17,
                'total' => 23.2,
                'taxes' => array(
                    'gst' => 1.01,
                    'qst' => 2.02,
                ),
            ),
            'service' => array(
                'flagship_code' => 'express',
                'courier_code' => 'FEDEX_EXPRESS_SAVER',
                'courier_desc' => 'Economy',
                'courier_name' => 'FedEx',
                'transit_time' => null,
                'estimated_delivery_date' => null,
            ),
            'labels' => array(
                'regular' => "https:\/\/s3.amazonaws.com\/waybills\/201609\/10e1e80cddbfd3f1f213d364ed7a2c0c\/reg_794631711299.pdf",
                'thermal' => "https:\/\/s3.amazonaws.com\/waybills\/201609\/10e1e80cddbfd3f1f213d364ed7a2c0c\/therm_794631711299.pdf",
            ),
            'packages' => array(
                0 => array(
                    'width' => '1',
                    'height' => '1',
                    'length' => '1',
                    'weight' => '2',
                    'description' => 'Flagship shipping package',
                    'pin' => '794631711299',
                ),
            ),

        ));

        $this->order->getShipment(true);

        foreach ($this->package['contents'] as $key => $package) {
            $this->package['contents'][$key]['data'] = \FS\Test\Helper\FlagshipShippingWooCommerceFactory::createSimpleProduct();
        }
    }

    public function testShoppingCartRateRequestFactory()
    {
        $factory = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Shipping\\Factory\\ShoppingCartRateRequestFactory');
        $options = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Options');

        $request = $factory->setPayload(array(
            'package' => $this->package,
            'options' => $options,
            'notifier' => $this->ctx->getComponent('\\FS\\Components\\Notifier'),
        ))->getRequest();

        $reflected = new \ReflectionClass('\\FS\\Components\\Shipping\\Factory\\FormattedRequestInterface');

        $this->assertTrue(true, $reflected->isInstance($request));
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
                'country' => 'CA',
                'state' => 'QC',
                'city' => 'Verdun',
                'postal_code' => 'H3E 1H2',
                'address' => '1460 N. MAIN STREET, # 9 ',
            ),
            'packages' => array(
                'items' => array(
                    0 => array(
                        'width' => 1,
                        'height' => 1,
                        'length' => 1,
                        'weight' => 2,
                        'description' => 'Flagship shipping package',
                    ),
                ),
                'units' => 'imperial',
                'type' => 'package',
            ),
            'payment' => array(
                'payer' => 'F',
            ),
            'options' => array(
                'address_correction' => true,
            ),
        ), $request->getRequest());
    }

    public function testShoppingOrderConfirmationRequestFactory()
    {
        $factory = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Shipping\\Factory\\ShoppingOrderConfirmationRequestFactory');
        $options = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Options');

        $request = $factory->setPayload(array(
            'order' => $this->order,
            'request' => $this->getApplicationContext()->getComponent('\\FS\\Components\\Web\\RequestParam'),
            'options' => $options,
        ))->getRequest();

        $reflected = new \ReflectionClass('\\FS\\Components\\Shipping\\Factory\\FormattedRequestInterface');

        $this->assertTrue(true, $reflected->isInstance($request));
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
            'options' => array(
                'address_correction' => true,
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
                            'description' => '',
                            'country_of_origin' => 'CA',
                            'quantity' => '4',
                            'unit_price' => '10',
                            'unit_weight' => 1,
                            'unit_of_measurement' => 'kilogram',
                        ),
                    ),
                ),
            ),
        ), $request->getRequest());
    }

    public function testShoppingOrderPickupRequestFactory()
    {
        $factory = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Shipping\\Factory\\ShoppingOrderPickupRequestFactory');
        $options = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Options');

        $request = $factory->setPayload(array(
            'order' => $this->order,
            'options' => $options,
            'shipment' => $this->order->getShipment(true)->getRawShipment(),
            'date' => $this->getApplicationContext()->getComponent('\\FS\\Components\\Web\\RequestParam')->request->get('flagship_shipping_pickup_schedule_date', '2016-09-28'),
        ))->getRequest();

        $reflected = new \ReflectionClass('\\FS\\Components\\Shipping\\Factory\\FormattedRequestInterface');

        $this->assertTrue(true, $reflected->isInstance($request));
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
            'courier' => 'fedex',
            'boxes' => 1,
            'weight' => 2,
            'date' => '2016-09-28',
            'from' => '09:00',
            'until' => '17:00',
            'units' => 'imperial',
            'location' => 'Reception',
            'to_country' => 'US',
            'is_ground' => false,
        ), $request->getRequest());
    }

    public function testShoppingOrderRateRequestFactory()
    {
        $factory = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Shipping\\Factory\\ShoppingOrderRateRequestFactory');
        $options = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Options');

        $request = $factory->setPayload(array(
            'order' => $this->order,
            'options' => $options,
        ))->getRequest();

        $reflected = new \ReflectionClass('\\FS\\Components\\Shipping\\Factory\\FormattedRequestInterface');

        $this->assertTrue(true, $reflected->isInstance($request));
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
            'options' => array(
                'address_correction' => true,
            ),
        ), $request->getRequest());
    }

    public function testMultipleOrdersPickupRequestFactory()
    {
        $factory = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Shipping\\Factory\\MultipleOrdersPickupRequestFactory');
        $options = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Options');

        $orderShippingsFactory = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Order\\Factory\\FlattenOrderShippingsFactory');

        $orders = array($this->order, clone $this->order);
        $flattenOrderShippings = $orderShippingsFactory->getFlattenOrderShippings($orders);

        $this->assertEquals(1, count($flattenOrderShippings));

        $flattenOrderShipping = $flattenOrderShippings[0];

        $this->assertEquals('fedex', $flattenOrderShipping['courier']);
        $this->assertEquals('international', $flattenOrderShipping['type']);
        $this->assertEquals(2, count($flattenOrderShipping['orders']));
        $this->assertEquals(2, count($flattenOrderShipping['ids']));
    }
}
