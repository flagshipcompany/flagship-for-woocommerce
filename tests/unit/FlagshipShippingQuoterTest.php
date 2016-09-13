<?php
/**
 * flagship shipping test case.
 */
class FlagshipShippingQuoterTest extends FlagshipShippingUnitTestCase
{
    protected $package;

    public function setUp()
    {
        parent::setUp();

        $this->package = require __DIR__.'/../fixtures/Package.php';
        $this->order = FlagshipShippingWooCommerceFactory::createSimpleOrder();
        $this->flagshipQuoteRates = require __DIR__.'/../fixtures/FlagshipQuoteRates.php';

        foreach ($this->package['contents'] as $key => $package) {
            $this->package['contents'][$key]['data'] = FlagshipShippingWooCommerceFactory::createSimpleProduct();
        }

        $this->ctx->load('Package');
        $this->ctx->load('Quoter');
    }

    public function testProtectedGetQuoteRequest()
    {
        $method = $this->setAccessProtectedMethod($this->ctx['quoter'], 'get_quote_request');
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
            ),
            'payment' => array(
                'payer' => 'F',
            ),
            'options' => array(
                'address_correction' => true,
            ),
        ), $method->invoke($this->ctx['quoter'], $this->package));
    }

    public function testProtectedGetRequoteRequest()
    {
        $method = $this->setAccessProtectedMethod($this->ctx['quoter'], 'get_requote_request');
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
            ),
            'payment' => array(
                'payer' => 'F',
            ),
            'options' => array(
                'address_correction' => true,
            ),
        ), $method->invoke($this->ctx['quoter'], $this->order));
    }

    public function testQuoterGetProcessedRates()
    {
        $this->assertSame(array(
            10 => array(
                'id' => 'flagship_shipping_method|Purolator|PurolatorExpress|Purolator Express|1473811200',
                'label' => 'Purolator - Purolator Express',
                'cost' => '11.97',
                'calc_tax' => 'per_order',
            ),
            11 => array(
                'id' => 'flagship_shipping_method|Purolator|PurolatorGround|Purolator Ground|1473811200',
                'label' => 'Purolator - Purolator Ground',
                'cost' => '16.54',
                'calc_tax' => 'per_order',
            ),
            0 => array(
                'id' => 'flagship_shipping_method|UPS|11|UPS Standard|1473895800',
                'label' => 'UPS - UPS Standard',
                'cost' => '17.78',
                'calc_tax' => 'per_order',
            ),
            1 => array(
                'id' => 'flagship_shipping_method|UPS|65|UPS Express Saver|1473865200',
                'label' => 'UPS - UPS Express Saver',
                'cost' => '17.97',
                'calc_tax' => 'per_order',
            ),
            5 => array(
                'id' => 'flagship_shipping_method|FedEx|STANDARD_OVERNIGHT|Standard Overnight|1473872400',
                'label' => 'FedEx - Standard Overnight',
                'cost' => '23.06',
                'calc_tax' => 'per_order',
            ),
            6 => array(
                'id' => 'flagship_shipping_method|FedEx|FEDEX_2_DAY|2 Days|1473872400',
                'label' => 'FedEx - 2 Days',
                'cost' => '23.06',
                'calc_tax' => 'per_order',
            ),
            7 => array(
                'id' => 'flagship_shipping_method|FedEx|FEDEX_EXPRESS_SAVER|Economy|1473872400',
                'label' => 'FedEx - Economy',
                'cost' => '23.06',
                'calc_tax' => 'per_order',
            ),
            9 => array(
                'id' => 'flagship_shipping_method|Purolator|PurolatorExpress10:30AM|Purolator Express 10:30 AM|1473811200',
                'label' => 'Purolator - Purolator Express 10:30 AM',
                'cost' => '24.13',
                'calc_tax' => 'per_order',
            ),
            4 => array(
                'id' => 'flagship_shipping_method|FedEx|PRIORITY_OVERNIGHT|Priority Overnight|1473854400',
                'label' => 'FedEx - Priority Overnight',
                'cost' => '24.23',
                'calc_tax' => 'per_order',
            ),
            2 => array(
                'id' => 'flagship_shipping_method|UPS|01|UPS Express|1473849000',
                'label' => 'UPS - UPS Express',
                'cost' => '28.61',
                'calc_tax' => 'per_order',
            ),
            8 => array(
                'id' => 'flagship_shipping_method|Purolator|PurolatorExpress9AM|Purolator Express 9AM|1473811200',
                'label' => 'Purolator - Purolator Express 9AM',
                'cost' => '32.88',
                'calc_tax' => 'per_order',
            ),
            3 => array(
                'id' => 'flagship_shipping_method|FedEx|FIRST_OVERNIGHT|First Overnight|1473847200',
                'label' => 'FedEx - First Overnight',
                'cost' => '40.52',
                'calc_tax' => 'per_order',
            ),
        ), $this->ctx['quoter']->get_processed_rates($this->flagshipQuoteRates));
    }
}
