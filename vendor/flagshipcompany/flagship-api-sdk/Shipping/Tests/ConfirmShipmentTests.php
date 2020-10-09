<?php
namespace Flagship\Shipping\Tests;

use \PHPUnit\Framework\TestCase;
use Flagship\Shipping\Objects\Shipment;

class ConfirmShipmentTests extends TestCase{

        public function testGetId(){
            $this->assertNotEmpty($this->confirmShipment->getId());
            $this->assertNotNull($this->confirmShipment->getId());
            $this->assertSame(2950364,$this->confirmShipment->getId());
        }


        public function testGetTrackingNumber(){
            $this->assertNotEmpty($this->confirmShipment->getTrackingNumber());
            $this->assertNotNull($this->confirmShipment->getTrackingNumber());
            $this->assertSame("329022136009",$this->confirmShipment->getTrackingNumber());
        }


        public function testIsDocumentsOnly(){
            $this->assertNotNull($this->confirmShipment->isDocumentsOnly());
            $this->assertSame(FALSE,$this->confirmShipment->isDocumentsOnly());
        }


        public function testGetSubtotal(){
            $this->assertNotEmpty($this->confirmShipment->getSubtotal());
            $this->assertNotNull($this->confirmShipment->getSubtotal());
            $this->assertSame(40.45,$this->confirmShipment->getSubtotal());
        }

        public function testGetTaxesDetails(){
            $expected = [
                "gst"=>2.02,
                "qst"=>4.04
            ];
            $this->assertNotNull($this->confirmShipment->getTaxesDetails());
            $this->assertNotEmpty($this->confirmShipment->getTaxesDetails());
            $this->assertSame($expected, $this->confirmShipment->getTaxesDetails());
        }


        public function testGetTotal(){
            $this->assertNotEmpty($this->confirmShipment->getTotal());
            $this->assertNotNull($this->confirmShipment->getTotal());
            $this->assertSame(46.51,$this->confirmShipment->getTotal());
        }

        public function testGetFlagshipCode(){
            $this->assertNotEmpty($this->confirmShipment->getFlagshipCode());
            $this->assertNotNull($this->confirmShipment->getFlagshipCode());
            $this->assertSame("standard",$this->confirmShipment->getFlagshipCode());
        }


        public function testGetCourierCode(){
            $this->assertNotEmpty($this->confirmShipment->getCourierCode());
            $this->assertNotNull($this->confirmShipment->getCourierCode());
            $this->assertSame("PurolatorGround",$this->confirmShipment->getCourierCode());
        }


        public function testGetCourierDescription(){
            $this->assertNotEmpty($this->confirmShipment->getCourierDescription());
            $this->assertNotNull($this->confirmShipment->getCourierDescription());
            $this->assertSame("Purolator Ground",$this->confirmShipment->getCourierDescription());
        }


        public function testGetCourierName(){
            $this->assertNotEmpty($this->confirmShipment->getCourierName());
            $this->assertNotNull($this->confirmShipment->getCourierName());
            $this->assertSame("Purolator",$this->confirmShipment->getCourierName());
        }


        public function testGetTransitTime(){
            $this->assertNotEmpty($this->confirmShipment->getTransitTime());
            $this->assertNotNull($this->confirmShipment->getTransitTime());
            $this->assertSame("1",$this->confirmShipment->getTransitTime());
        }


        public function testGetEstimatedDeliveryDate(){
            $this->assertNotEmpty($this->confirmShipment->getEstimatedDeliveryDate());
            $this->assertNotNull($this->confirmShipment->getEstimatedDeliveryDate());
            $this->assertSame("2018-11-02",$this->confirmShipment->getEstimatedDeliveryDate());
        }


        public function testGetLabel(){
            $this->assertNotEmpty($this->confirmShipment->getLabel());
            $this->assertNotNull($this->confirmShipment->getLabel());
            $this->assertSame("https://flagshipcompany.com/ship/2950364/labels/06df987f0d2ef55d19da283baebaa12771e46a8f?document=reg",$this->confirmShipment->getLabel());
        }


        public function testGetThermalLabel(){
            $this->assertNotEmpty($this->confirmShipment->getThermalLabel());
            $this->assertNotNull($this->confirmShipment->getThermalLabel());
            $this->assertSame("https://flagshipcompany.com/ship/2950364/labels/06df987f0d2ef55d19da283baebaa12771e46a8f?document=therm",$this->confirmShipment->getThermalLabel());
        }

        protected function setUp(){
            $response = '{
               "shipment_id":2950364,
               "tracking_number":"329022136009",
               "price":{
                  "charges":{
                     "freight":19.43,
                     "fuel_surcharge":2.27,
                     "insurance":18.75
                  },
                  "adjustments":null,
                  "debits":null,
                  "brokerage":null,
                  "subtotal":40.45,
                  "total":46.51,
                  "taxes":{
                     "gst":2.02,
                     "qst":4.04
                  }
               },
               "service":{
                  "flagship_code":"standard",
                  "courier_code":"PurolatorGround",
                  "courier_desc":"Purolator Ground",
                  "courier_name":"Purolator",
                  "transit_time":1,
                  "estimated_delivery_date":"2018-11-02"
               },
               "labels":{
                  "regular":"https:\/\/flagshipcompany.com\/ship\/2950364\/labels\/06df987f0d2ef55d19da283baebaa12771e46a8f?document=reg",
                  "thermal":"https:\/\/flagshipcompany.com\/ship\/2950364\/labels\/06df987f0d2ef55d19da283baebaa12771e46a8f?document=therm"
               },
               "packages":[
                  {
                     "width":22,
                     "height":15,
                     "length":7,
                     "weight":10,
                     "description":null,
                     "pin":"329022136009"
                  }
               ],
               "documents_only":0
            }';

            $this->confirmShipment = $this->getMockBuilder(Shipment::class)
                              ->setConstructorArgs([json_decode($response)])
                              ->setMethods(['__construct']) 
                              ->getMock();

        }
}