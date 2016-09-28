<?php

namespace FS\Shipping;

class RateProcessorTestCase extends \FlagshipShippingUnitTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->flagshipQuoteRates = require __DIR__.'/../fixtures/FlagshipQuoteRates.php';
    }

    public function testRateProcessorProtectedFilterByEnabledType()
    {
        $options = $this->getApplicationContext()->getComponent('\\FS\\Components\\Options');
        $processor = $this->getApplicationContext()->getComponent('\\FS\\Components\\Shipping\\RateProcessor');

        $options->set('allow_express_rates', 'no');
        $options->set('allow_overnight_rates', 'no');

        $rates = $processor->convertToWcShippingRate($this->flagshipQuoteRates);

        foreach ($rates as $rate) {
            $this->assertRegexp('/(standard|ground)/i', $rate['id']);
        }
    }

    public function testRateProcessorSampleConvertToWcShippingRate()
    {
        $options = $this->getApplicationContext()->getComponent('\\FS\\Components\\Options');
        $processor = $this->getApplicationContext()->getComponent('\\FS\\Components\\Shipping\\RateProcessor');

        $options->set('allow_express_rates', 'yes');
        $options->set('allow_overnight_rates', 'yes');
        $options->set('allow_standard_rates', 'yes');

        $rates = $processor->convertToWcShippingRate($this->flagshipQuoteRates);

        $wcRate = array();

        foreach ($rates as $rate) {
            if ($rate['label'] == 'Purolator - Purolator Express') {
                $wcRate = $rate;
                break;
            }
        }

        $this->assertSame(array(
            'id' => 'flagship_shipping_method|Purolator|PurolatorExpress|Purolator Express|1473811200|0',
            'label' => 'Purolator - Purolator Express',
            'cost' => '11.97',
            'calc_tax' => 'per_order',
        ), $wcRate);
    }
}
