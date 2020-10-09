<?php
use FlagshipWoocommerce\Requests\Rates_Request;

class RatesTest extends FlagshipShippingUnitTestCase
{
    public function setUp()
    {
        $this->package = require __DIR__.'/../fixture/Package.php';
        $this->zoneSettings = require __DIR__.'/../fixture/ZoneSettings.php';
        parent::setUp();
    }

    public function testGetRates()
    {
        $order = $this->getOrder();
        $rateRequest = new Rates_Request($this->zoneSettings['token'],false,1);
        $rates = $rateRequest->getRates($this->package,$this->zoneSettings,1,$order);

        $this->assertCount(0,$rates);
    }


    protected function getOrder(){
        $product = $this->generate_simple_product();

        $order = new WC_Order();
        $address = array(
            'first_name' => 'Fresher',
            'last_name'  => 'StAcK OvErFloW',
            'company'    => 'stackoverflow',
            'email'      => 'test@test.com',
            'phone'      => '777-777-777-777',
            'address_1'  => '31 Main Street',
            'address_2'  => '',
            'city'       => 'Chennai',
            'state'      => 'TN',
            'postcode'   => '12345',
            'country'    => 'IN'
        );


        $order->add_product( $product, 1 ); //(get_product with id and next is for quantity)
        $order->set_address( $address, 'billing' );
        $order->set_address( $address, 'shipping' );

        $order->calculate_totals();
        $order->save();
        return $order;

    }

    protected function generate_simple_product() {
        $name              = 'My Product Name';
        $will_manage_stock = true;
        $is_virtual        = false;
        $price             = 1000.00;
        $is_on_sale        = true;
        $sale_price        = 999.00;
        $product           = new \WC_Product();
        $product->set_props( array(
            'name'               => $name,
            'featured'           => false,
            'catalog_visibility' => 'visible',
            'description'        => 'My awesome product description',
            'short_description'  => 'My short description',
            'sku'                => sanitize_title( $name ) . '-' . rand(0, 100), // Just an example
            'regular_price'      => $price,
            'sale_price'         => $sale_price,
            'date_on_sale_from'  => '',
            'date_on_sale_to'    => '',
            'total_sales'        => 0,
            'tax_status'         => 'taxable',
            'tax_class'          => '',
            'manage_stock'       => $will_manage_stock,
            'stock_quantity'     => $will_manage_stock ? 100 : null, // Stock quantity or null
            'stock_status'       => 'instock',
            'backorders'         => 'no',
            'sold_individually'  => true,
            'weight'             => $is_virtual ? '' : 1,
            'length'             => $is_virtual ? '' : 15,
            'width'              => $is_virtual ? '' : 15,
            'height'             => $is_virtual ? '' : 15,
            'upsell_ids'         => '',
            'cross_sell_ids'     => '',
            'parent_id'          => 0,
            'reviews_allowed'    => true,
            'purchase_note'      => '',
            'menu_order'         => 10,
            'virtual'            => $is_virtual,
            'downloadable'       => false,
            'category_ids'       => [],
            'tag_ids'            => [],
            'shipping_class_id'  => 0,
        ) );

        $product->save();

        return $product;
    }

}
