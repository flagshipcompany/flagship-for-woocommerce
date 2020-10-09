<?php
namespace Flagship\Shipping\Tests;

use \PHPUnit\Framework\TestCase;
use Flagship\Shipping\Objects\Rate;

class RateTests extends TestCase{

    public function testGetTotal(){
        $this->assertNotNull($this->rate->getTotal());
        $this->assertSame(64.78, $this->rate->getTotal());
    }

    public function testGetSubTotal(){
        $this->assertNotNull($this->rate->getSubTotal());
        $this->assertSame(56.34, $this->rate->getSubTotal());
    }

    public function testGetTaxesTotal(){
        $this->assertNotNull($this->rate->getTaxesTotal());
        $this->assertSame(8.44, $this->rate->getTaxesTotal());
    }

    public function testGetTaxesDetails(){
        $expected = [
            "gst"=> 2.82,
            "qst"=> 5.62
        ];
        $this->assertNotNull($this->rate->getTaxesDetails());
        $this->assertInternalType('array', $this->rate->getTaxesDetails());
        $this->assertSame($expected, $this->rate->getTaxesDetails());
    }

    public function testGetServiceCode(){
        $this->assertNotNull($this->rate->getServiceCode());
        $this->assertSame('65', $this->rate->getServiceCode());
    }

    public function testGetDeliveryDate(){
        $this->assertNotNull($this->rate->getDeliveryDate());
        $this->assertSame('2018-12-12 15:00', $this->rate->getDeliveryDate());
    }

    public function testGetCourierName(){
        $this->assertNotNull($this->rate->getCourierName());
        $this->assertSame('UPS',$this->rate->getCourierName());
    }

    public function testGetTransitTime(){
        $this->assertSame('1',$this->rate->getTransitTime());
    }

    public function testGetFlagshipCode(){
        $this->assertNotNull($this->rate->getFlagshipCode());
        $this->assertSame('expressAm',$this->rate->getFlagshipCode());
    }

    public function testGetBrokerage(){
        $this->assertSame(null,$this->rate->getBrokerage());
    }

    public function testGetDebits(){
        $this->assertSame(null,$this->rate->getDebits());
    }

    public function testGetAdjustments(){
        $this->assertSame(null,$this->rate->getAdjustments());
    }

    protected function setUp(){
        $response = '{
                "price": {
                    "charges": {
                        "freight": 51.39,
                        "insurance": 4.95
                    },
                    "adjustments": null,
                    "debits": null,
                    "brokerage": null,
                    "subtotal": 56.34,
                    "total": 64.78,
                    "taxes": {
                        "gst": 2.82,
                        "qst": 5.62
                    }
                },
                "service": {
                    "flagship_code": "expressAm",
                    "courier_code": "65",
                    "courier_desc": "UPS Express Saver",
                    "courier_name": "UPS",
                    "transit_time": 1,
                    "estimated_delivery_date": "2018-12-12 15:00"
                }
            }';

        $this->rate = $this->getMockBuilder(Rate::class)
                          ->setConstructorArgs([json_decode($response)])
                          ->setMethods(['__construct'])
                          ->getMock();

    }

}
