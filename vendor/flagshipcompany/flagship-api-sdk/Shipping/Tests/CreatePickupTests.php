<?php

namespace Flagship\Shipping\Tests;

use \PHPUnit\Framework\TestCase;
use Flagship\Shipping\Requests\CreatePickupRequest;
use Flagship\Shipping\Exceptions\CreatePickupException;
use Flagship\Shipping\Objects\Pickup;

class CreatePickupTests extends TestCase{

    public function testGetId(){
        $this->assertNotNull($this->pickup->getId());
        $this->assertSame(1276084,$this->pickup->getId());
    }

    public function testGetConfirmation(){
        $this->assertNotNull($this->pickup->getConfirmation());
        $this->assertSame("3145714",$this->pickup->getConfirmation());
    }

    public function testGetFullAddress(){
        $this->assertNotNull($this->pickup->getFullAddress());
        $this->assertSame([
                    "name"=> "FCS",
                    "attn"=> "customer service",
                    "address"=> "148 brunswick boul",
                    "suite"=> null,
                    "city"=> "Pointe-Claire",
                    "country"=> "CA",
                    "state"=> "QC",
                    "postal_code"=> "H9R5P9",
                    "phone"=> "5147390202",
                    "ext"=>null,
                    "is_commercial"=> "1"
                ],$this->pickup->getFullAddress());
    }

    public function testGetAddress(){
        $this->assertNotNull($this->pickup->getAddress());
        $this->assertSame("148 brunswick boul",$this->pickup->getAddress());
    }

    public function testGetCompany(){
        $this->assertNotNull($this->pickup->getCompany());
        $this->assertSame("FCS",$this->pickup->getCompany());
    }

    public function testGetName(){
        $this->assertNotNull($this->pickup->getName());
        $this->assertSame("customer service",$this->pickup->getName());
    }

    public function testGetSuite(){
        $this->assertNotNull($this->pickup->getSuite());
        $this->assertSame("",$this->pickup->getSuite());
    }

    public function testGetCity(){
        $this->assertNotNull($this->pickup->getCity());
        $this->assertSame("Pointe-Claire",$this->pickup->getCity());
    }

    public function testGetCountry(){
        $this->assertNotNull($this->pickup->getCountry());
        $this->assertSame("CA",$this->pickup->getCountry());
    }

    public function testGetState(){
        $this->assertNotNull($this->pickup->getState());
        $this->assertSame("QC",$this->pickup->getState());
    }

    public function testGetPostalCode(){
        $this->assertNotNull($this->pickup->getPostalCode());
        $this->assertSame("H9R5P9",$this->pickup->getPostalCode());
    }

    public function testGetPhone(){
        $this->assertNotNull($this->pickup->getPhone());
        $this->assertSame("5147390202",$this->pickup->getPhone());
    }

    public function testGetPhoneExt(){
        $this->assertNotNull($this->pickup->getPhoneExt());
        $this->assertSame('',$this->pickup->getPhoneExt());
    }

    public function testIsAddressCommercial(){
        $this->assertNotNull($this->pickup->isAddressCommercial());
        $this->assertSame(TRUE,$this->pickup->isAddressCommercial());
    }

    public function testGetCourier(){
        $this->assertNotNull($this->pickup->getCourier());
        $this->assertSame("canpar",$this->pickup->getCourier());
    }

    public function testGetUnits(){
        $this->assertNotNull($this->pickup->getUnits());
        $this->assertSame("imperial",$this->pickup->getUnits());
    }

    public function testGetBoxes(){
        $this->assertNotNull($this->pickup->getBoxes());
        $this->assertSame("1",$this->pickup->getBoxes());
    }

    public function testGetWeight(){
        $this->assertNotNull($this->pickup->getWeight());
        $this->assertSame("1",$this->pickup->getWeight());
    }

    public function testGetPickupLocation(){
        $this->assertNotNull($this->pickup->getPickupLocation());
        $this->assertSame("FrontDesk",$this->pickup->getPickupLocation());
    }

    public function testGetDate(){
        $this->assertNotNull($this->pickup->getDate());
        $this->assertSame("2019-12-13",$this->pickup->getDate());
    }

    public function testGetFromTime(){
        $this->assertNotNull($this->pickup->getFromTime());
        $this->assertSame("08:00",$this->pickup->getFromTime());
    }

    public function testGetUntilTime(){
        $this->assertNotNull($this->pickup->getUntilTime());
        $this->assertSame("17:00",$this->pickup->getUntilTime());
    }

    public function testGetToCountry(){
        $this->assertnotNull($this->pickup->getToCountry());
        $this->assertSame("CA",$this->pickup->getToCountry());
    }

    public function testGetInstructions(){
        $this->assertNull($this->pickup->getInstructions());
    }

    public function testIsCancelled(){
        $this->assertNotNull($this->pickup->isCancelled());
        $this->assertSame(FALSE,$this->pickup->isCancelled());
    }

    protected function setUp(){

        $response = '{
                "id": "1276084",
                "confirmation": "3145714",
                "address": {
                    "name": "FCS",
                    "attn": "customer service",
                    "address": "148 brunswick boul",
                    "suite": null,
                    "city": "Pointe-Claire",
                    "country": "CA",
                    "state": "QC",
                    "postal_code": "H9R5P9",
                    "phone": "5147390202",
                    "ext": null,
                    "is_commercial": "1"
                },
                "courier": "canpar",
                "units": "imperial",
                "boxes": "1",
                "weight": "1",
                "location": "FrontDesk",
                "date": "2019-12-13",
                "from": "08:00",
                "until": "17:00",
                "to_country": "CA",
                "instruction": null,
                "cancelled": false
            }';

        $this->createPickupRequest = $this->getMockBuilder(CreatePickupRequest::class)
            ->setConstructorArgs(['testToken','localhost',[],'test','1.0.11'])
            ->setMethods(['execute'])
            ->getMock();
        $this->createPickup = $this->createPickupRequest->execute();
        $this->pickup = new Pickup(json_decode($response));
    }
}
