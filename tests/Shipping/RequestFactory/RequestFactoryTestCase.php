<?php

namespace FS\Shipping\RequestFactory;

use FS\Test\Helper\FlagshipShippingUnitTestCase;
use FS\Test\Helper\FlagshipShippingWooCommerceFactory;
use FS\Components\Shipping\Factory\ShippingFactory;

class RequestFactoryTestCase extends FlagshipShippingUnitTestCase
{
    protected $testShipmentId = 1517028;

    public function setUp()
    {
        parent::setUp();

        $this->package = require __DIR__.'/../../Fixture/Package.php';
        $this->shipping = $this->getApplicationContext()
            ->_('\\FS\\Components\\Shipping\\Factory\\ShippingFactory')
            ->resolve(ShippingFactory::RESOURCE_SHIPPING, [
                'native_order' => FlagshipShippingWooCommerceFactory::createSimpleOrder(),
            ]);

        $this->shipping->getShipment()->set([
            'shipment_id' => $this->testShipmentId,
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
        ]);

        foreach ($this->package['contents'] as $key => $package) {
            $this->package['contents'][$key]['data'] = FlagshipShippingWooCommerceFactory::createSimpleProduct();
        }
    }

    public function testShoppingCartRateRequestFactory()
    {
        $factory = $this->getApplicationContext()
            ->_('\\FS\\Components\\Shipping\\Request\\Factory\\ShoppingCartRate');
        $options = $this->getApplicationContext()
            ->_('\\FS\\Components\\Options');

        $request = $factory->setPayload(array(
            'package' => $this->package,
            'options' => $options,
            'notifier' => $this->ctx->_('\\FS\\Components\\Alert\\Notifier'),
        ))->getRequest();

        $reflected = new \ReflectionClass('\\FS\\Components\\Shipping\\Request\\FormattedRequestInterface');

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
                'is_commercial' => false,
            ),
            'packages' => array(
                'items' => array(
                    0 => array(
                        'width' => 1,
                        'height' => 1,
                        'length' => 1,
                        'weight' => 1,
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

    public function testShopOrderConfirmationRequestFactory()
    {
        $factory = $this->getApplicationContext()
            ->_('\\FS\\Components\\Shipping\\Request\\Factory\\ShoppingOrderConfirmation');
        $options = $this->getApplicationContext()
            ->option();

        $request = $factory->setPayload([
            'shipping' => $this->shipping,
            'request' => $this->getApplicationContext()->_('\\FS\\Components\\Web\\RequestParam'),
            'options' => $options,
        ])->getRequest();

        $reflected = new \ReflectionClass('\\FS\\Components\\Shipping\\Request\\FormattedRequestInterface');

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
                'is_commercial' => false,
            ),
            'packages' => array(
                'items' => array(
                    0 => array(
                        'width' => 1,
                        'height' => 1,
                        'length' => 1,
                        'weight' => 1,
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
                    'is_commercial' => false,
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
                        'quantity' => 4,
                        'unit_price' => '10',
                        'unit_weight' => 1,
                        'unit_of_measurement' => 'kilogram',
                    ),
                ),
            ),
        ), $request->getRequest());
    }

    public function testShopOrderPickupRequestFactory()
    {
        $factory = $this->getApplicationContext()
            ->_('\\FS\\Components\\Shipping\\Request\\Factory\\ShoppingOrderPickup');
        $options = $this->getApplicationContext()
            ->_('\\FS\\Components\\Options');

        $request = $factory->setPayload(array(
            'shipping' => $this->shipping,
            'options' => $options,
            'date' => $this->getApplicationContext()->_('\\FS\\Components\\Web\\RequestParam')->request->get('flagship_shipping_pickup_schedule_date', '2016-09-28'),
        ))->getRequest();

        $reflected = new \ReflectionClass('\\FS\\Components\\Shipping\\Request\\FormattedRequestInterface');

        $this->assertTrue(true, $reflected->isInstance($request));
        $this->assertSame(array(
            'shipments' => array($this->testShipmentId),
            'date' => '2016-09-28',
            'from' => '09:00',
            'until' => '17:00',
            'location' => 'Reception',
        ), $request->getRequest());
    }

    public function testShopOrderRateRequestFactory()
    {
        $factory = $this->getApplicationContext()
            ->_('\\FS\\Components\\Shipping\\Request\\Factory\\ShoppingOrderRate');
        $options = $this->getApplicationContext()
            ->_('\\FS\\Components\\Options');

        $request = $factory->setPayload(array(
            'shipping' => $this->shipping,
            'options' => $options,
        ))->getRequest();

        $reflected = new \ReflectionClass('\\FS\\Components\\Shipping\\Request\\FormattedRequestInterface');

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
                'is_commercial' => false,
            ),
            'packages' => array(
                'items' => array(
                    0 => array(
                        'width' => 1,
                        'height' => 1,
                        'length' => 1,
                        'weight' => 1,
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
            ->_('\\FS\\Components\\Shipping\\Request\\Factory\\MultipleOrdersPickup');
        $options = $this->getApplicationContext()
            ->_('\\FS\\Components\\Options');

        $regroupShippingsFactory = $this->getApplicationContext()
            ->_('\\FS\\Components\\Shipping\\Factory\\RegroupShippingsFactory');

        $shippings = array($this->shipping, clone $this->shipping);
        $regroupedShippings = $regroupShippingsFactory->getRegroupedShippings($shippings);

        $this->assertEquals(1, count($regroupedShippings));

        $regroupedShipping = $regroupedShippings[0];

        $this->assertEquals('fedex', $regroupedShipping['courier']);
        $this->assertEquals('international', $regroupedShipping['type']);
        $this->assertEquals(2, count($regroupedShipping['shippings']));
        $this->assertEquals(2, count($regroupedShipping['ids']));
    }
}
