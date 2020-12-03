<?php

namespace FS\Test\Shipping\RateProcessor;

use FS\Components\Options;
use FS\Components\Shipping\Object\Courier;

class RateProcessorTestCase extends \FS\Test\Helper\FlagshipShippingUnitTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->flagshipQuoteRates = require __DIR__.'/../../Fixture/FlagshipQuoteRates.php';
    }

    public function testCourierExcludedRateProcessor()
    {
        $options = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Options');

        $this->disableEnableCouriers($options, true);

        $processor = new \FS\Components\Shipping\RateProcessor\CourierExcludedRateProcessor();
        $rates = $processor->getProcessedRates($this->flagshipQuoteRates, array(
            'excluded' => array_filter(array('fedex', 'ups', 'purolator'), function ($courier) use ($options) {
                return $options->neq('disable_courier_'.$courier, 'no');
            }),
        ));

        $this->assertEquals(0, count($rates));

        $this->disableEnableCouriers($options, false);
        $this->disableEnableCouriers($options, true, 'ups');

        $rates = $processor->getProcessedRates($this->flagshipQuoteRates, array(
            'excluded' => array_filter(Courier::$couriers, function ($courier) use ($options) {
                return $options->neq('disable_courier_'.$courier, 'no');
            }),
        ));

        $this->assertEquals(9, count($rates));

        foreach ($rates as $rate) {
            $this->assertTrue(strtolower($rate['service']['courier_name']) != 'ups');
        }
    }

    public function testCourierEnabledRateProcessor()
    {
        $options = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Options');

        $options->set('allow_express_rates', 'no');
        $options->set('allow_overnight_rates', 'no');
        $options->set('allow_standard_rates', 'no');

        $processor = new \FS\Components\Shipping\RateProcessor\EnabledRateProcessor();
        $rates = $processor->getProcessedRates($this->flagshipQuoteRates, array(
            'enabled' => array(
                'standard' => ($options->get('allow_standard_rates') == 'yes'),
                'express' => ($options->get('allow_express_rates') == 'yes'),
                'overnight' => ($options->get('allow_overnight_rates') == 'yes'),
            ),
        ));

        $this->assertEquals(0, count($rates));

        $options->set('allow_express_rates', 'yes');
        $options->set('allow_overnight_rates', 'no');
        $options->set('allow_standard_rates', 'yes');

        $enabled = array(
            'standard' => ($options->get('allow_standard_rates') == 'yes'),
            'express' => ($options->get('allow_express_rates') == 'yes'),
            'overnight' => ($options->get('allow_overnight_rates') == 'yes'),
        );

        $rates = $processor->getProcessedRates($this->flagshipQuoteRates, array(
            'enabled' => $enabled,
        ));

        $this->assertEquals(6, count($rates));

        foreach ($rates as $rate) {
            $this->assertTrue(in_array(\FS\Components\Shipping\RateProcessor\EnabledRateProcessor::$mapping[$rate['service']['flagship_code']], $enabled));
        }
    }

    public function testXNumberOfBestRateProcessor()
    {
        $options = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Options');

        $options->set('offer_rates', 'all');

        $processor = new \FS\Components\Shipping\RateProcessor\XNumberOfBestRateProcessor();
        $allRates = $processor->getProcessedRates($this->flagshipQuoteRates, array(
            'taxEnabled' => ($options->get('apply_tax_by_flagship') == 'yes'),
            'offered' => $options->get('offer_rates', 'all'),
        ));

        $this->assertEquals(12, count($allRates));

        $options->set('offer_rates', 'cheapest');

        $processor = new \FS\Components\Shipping\RateProcessor\XNumberOfBestRateProcessor();
        $rates = $processor->getProcessedRates($this->flagshipQuoteRates, array(
            'taxEnabled' => ($options->get('apply_tax_by_flagship') == 'yes'),
            'offered' => $options->get('offer_rates', 'all'),
        ));

        $this->assertEquals(1, count($rates));

        $options->set('offer_rates', '5');

        $processor = new \FS\Components\Shipping\RateProcessor\XNumberOfBestRateProcessor();
        $rates = $processor->getProcessedRates($this->flagshipQuoteRates, array(
            'taxEnabled' => ($options->get('apply_tax_by_flagship') == 'yes'),
            'offered' => $options->get('offer_rates', 'all'),
        ));

        $this->assertEquals(5, count($rates));

        $remainings = array_slice($allRates, 5);

        foreach ($remainings as $remaining) {
            $err = array_filter($rates, function ($rate) use ($remaining) {
                return $rate['price']['total'] > $remaining['price']['total'];
            });

            $this->assertEquals(0, count($err));
        }
    }

    protected function disableEnableCouriers(Options $options, $disable, $courier = null)
    {
        $optionValue = $disable == true ? 'yes' : 'no';

        if ($courier) {
            $options->set('disable_courier_'.$courier, $optionValue);

            return;
        }

        foreach (Courier::$couriers as $key => $value) {
            $options->set('disable_courier_'.$value, $optionValue);
        }
    }
}
