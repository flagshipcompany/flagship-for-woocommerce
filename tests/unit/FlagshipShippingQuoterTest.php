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
        $wcRates = $this->ctx['quoter']->get_processed_rates($this->flagshipQuoteRates);

        $wcRate = array();

        foreach ($wcRates as $rate) {
            if ($rate['label'] == 'Purolator - Purolator Express') {
                $wcRate = $rate;
                break;
            }
        }

        $this->assertSame(array(
            'id' => 'flagship_shipping_method|Purolator|PurolatorExpress|Purolator Express|1473811200',
            'label' => 'Purolator - Purolator Express',
            'cost' => '11.97',
            'calc_tax' => 'per_order',
        ), $wcRate);
    }
}
