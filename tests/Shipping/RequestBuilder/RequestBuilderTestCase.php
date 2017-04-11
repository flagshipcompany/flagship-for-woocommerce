<?php

namespace FS\Test\Shipping\RequestBuilder;

use FS\Test\Helper\FlagshipShippingUnitTestCase;
use FS\Test\Helper\FlagshipShippingWooCommerceFactory;
use FS\Components\Shipping\Factory\ShippingFactory;

class RequestBuilderTestCase extends FlagshipShippingUnitTestCase
{
    protected $package;
    protected $shipping;

    public function setUp()
    {
        parent::setUp();

        $this->package = require __DIR__.'/../../Fixture/Package.php';
        $this->shipping = $this->getApplicationContext()
            ->_('\\FS\\Components\\Shipping\\Factory\\ShippingFactory')
            ->resolve(ShippingFactory::RESOURCE_SHIPPING, [
                'native_order' => FlagshipShippingWooCommerceFactory::createSimpleOrder(),
            ]);

        foreach ($this->package['contents'] as $key => $package) {
            $this->package['contents'][$key]['data'] = FlagshipShippingWooCommerceFactory::createSimpleProduct();
        }
    }

    public function testShipperAddressRequestBuilder()
    {
        $builder = new \FS\Components\Shipping\Request\Builder\ShipperAddressBuilder();
        $options = $this->ctx->getComponent('\\FS\\Components\\Options');

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
        ), $builder->build(array('options' => $options)));
    }

    public function testShoppingCartPackageItemsBuilder()
    {
        $builder = new \FS\Components\Shipping\Request\Builder\Cart\PackageItems\FallbackBuilder();

        $this->assertSame(array(
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
        ), $builder->build(array(
            'package' => $this->package,
            'options' => $this->ctx->getComponent('\\FS\\Components\\Options'),
            'notifier' => $this->ctx->getComponent('\\FS\\Components\\Notifier'),
        )));
    }

    public function testShoppingCartReceiverAddressBuilder()
    {
        $builder = new \FS\Components\Shipping\Request\Builder\Cart\ReceiverAddressBuilder();

        $this->assertSame(array(
            'country' => 'CA',
            'state' => 'QC',
            'city' => 'Verdun',
            'postal_code' => 'H3E 1H2',
            'address' => '1460 N. MAIN STREET, # 9 ',
        ), $builder->build(array(
            'package' => $this->package,
        )));
    }

    public function testShopOrderPackageItemsBuilder()
    {
        $builder = new \FS\Components\Shipping\Request\Builder\Order\PackageItems\FallbackBuilder();

        $this->assertSame(array(
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
        ), $builder->build(array(
            'shipping' => $this->shipping,
            'options' => $this->ctx->getComponent('\\FS\\Components\\Options'),
        )));
    }

    public function testShopOrderReceiverAddressBuilder()
    {
        $builder = new \FS\Components\Shipping\Request\Builder\Order\ReceiverAddressBuilder();

        $this->assertSame(array(
            'name' => 'WooCompany',
            'attn' => 'Jeroen Sormani',
            'address' => 'WooAddress',
            'city' => 'WooCity',
            'state' => 'NY',
            'country' => 'US',
            'postal_code' => '123456',
            'phone' => '',
        ), $builder->build(array(
            'shipping' => $this->shipping,
        )));
    }
}
