<?php
namespace Flagship\Shipping\Tests;

use \PHPUnit\Framework\TestCase;
use Flagship\Shipping\Objects\Pickup;

class EditPickupTests extends TestCase{

  public function testGetId(){
        $this->assertNotEmpty($this->editPickup->getId());
        $this->assertNotNull($this->editPickup->getId());
        $this->assertSame(1085704,$this->editPickup->getId());
    }

    public function testGetConfirmation(){
        $this->assertNotEmpty($this->editPickup->getConfirmation());
        $this->assertNotNull($this->editPickup->getConfirmation());
        $this->assertSame("2929602E9CP",$this->editPickup->getConfirmation());
    }


    public function testGetFullAddress(){
        $expectedResult = [
            "name"  => "fls",
            "attn"  => "ryan",
            "address" => "3767 thimens",
            "suite"=> "227",
            "city"=> "SAINT-LAURENT",
            "country"=> "CA",
            "state"=> "QC",
            "postal_code"=> "H4R1W4",
            "phone"=> "5146789056",
            "ext"=> "11",
            "is_commercial"=> "1"
        ];

        $this->assertNotEmpty($this->editPickup->getFullAddress());
        $this->assertNotNull($this->editPickup->getFullAddress());
        $this->assertSame($expectedResult,$this->editPickup->getFullAddress());
    }


    public function testGetAddress(){
        $this->assertNotEmpty($this->editPickup->getAddress());
        $this->assertNotNull($this->editPickup->getAddress());
        $this->assertSame("3767 thimens",$this->editPickup->getAddress());
    }


    public function testGetCompany(){
        $this->assertNotEmpty($this->editPickup->getCompany());
        $this->assertNotNull($this->editPickup->getCompany());
        $this->assertSame("fls",$this->editPickup->getCompany());
    }


    public function testGetName(){
        $this->assertNotEmpty($this->editPickup->getName());
        $this->assertNotNull($this->editPickup->getName());
        $this->assertSame("ryan",$this->editPickup->getName());
    }


    public function testGetSuite(){
        $this->assertNotEmpty($this->editPickup->getSuite());
        $this->assertNotNull($this->editPickup->getSuite());
        $this->assertSame("227",$this->editPickup->getSuite());
    }


    public function testGetCity(){
        $this->assertNotEmpty($this->editPickup->getCity());
        $this->assertNotNull($this->editPickup->getCity());
        $this->assertSame("SAINT-LAURENT",$this->editPickup->getCity());
    }


    public function testGetCountry(){
        $this->assertNotEmpty($this->editPickup->getCountry());
        $this->assertNotNull($this->editPickup->getCountry());
        $this->assertSame("CA",$this->editPickup->getCountry());
    }


    public function testGetState(){
        $this->assertNotEmpty($this->editPickup->getState());
        $this->assertNotNull($this->editPickup->getState());
        $this->assertSame("QC",$this->editPickup->getState());
    }


    public function testGetPostalCode(){
        $this->assertNotEmpty($this->editPickup->getPostalCode());
        $this->assertNotNull($this->editPickup->getPostalCode());
        $this->assertSame("H4R1W4",$this->editPickup->getPostalCode());
    }


    public function testGetPhone(){
        $this->assertNotEmpty($this->editPickup->getPhone());
        $this->assertNotNull($this->editPickup->getPhone());
        $this->assertSame("5146789056",$this->editPickup->getPhone());
    }


    public function testGetPhoneExt(){
        $this->assertNotEmpty($this->editPickup->getPhoneExt());
        $this->assertNotNull($this->editPickup->getPhoneExt());
        $this->assertSame("11",$this->editPickup->getPhoneExt());
    }


    public function testIsAddressCommercial(){
        $this->assertNotEmpty($this->editPickup->isAddressCommercial());
        $this->assertNotNull($this->editPickup->isAddressCommercial());
        $this->assertSame(TRUE,$this->editPickup->isAddressCommercial());
    }


    public function testGetCourier(){
        $this->assertNotEmpty($this->editPickup->getCourier());
        $this->assertNotNull($this->editPickup->getCourier());
        $this->assertSame("ups",$this->editPickup->getCourier());
    }


    public function testGetUnits(){
        $this->assertNotEmpty($this->editPickup->getUnits());
        $this->assertNotNull($this->editPickup->getUnits());
        $this->assertSame("imperial",$this->editPickup->getUnits());
    }


    public function testGetBoxes(){
        $this->assertNotEmpty($this->editPickup->getBoxes());
        $this->assertNotNull($this->editPickup->getBoxes());
        $this->assertSame("2",$this->editPickup->getBoxes());
    }


    public function testGetWeight(){
        $this->assertNotEmpty($this->editPickup->getWeight());
        $this->assertNotNull($this->editPickup->getWeight());
        $this->assertSame("8",$this->editPickup->getWeight());
    }


    public function testGetPickupLocation(){
        $this->assertNotEmpty($this->editPickup->getPickupLocation());
        $this->assertNotNull($this->editPickup->getPickupLocation());
        $this->assertSame("FrontDesk",$this->editPickup->getPickupLocation());
    }


    public function testGetDate(){
        $this->assertNotEmpty($this->editPickup->getDate());
        $this->assertNotNull($this->editPickup->getDate());
        $this->assertSame("2018-11-05",$this->editPickup->getDate());
    }


    public function testGetFromTime(){
        $this->assertNotEmpty($this->editPickup->getFromTime());
        $this->assertNotNull($this->editPickup->getFromTime());
        $this->assertSame("09:00:00",$this->editPickup->getFromTime());
    }


    public function testGetUntilTime(){
        $this->assertNotEmpty($this->editPickup->getUntilTime());
        $this->assertNotNull($this->editPickup->getUntilTime());
        $this->assertSame("15:00:00",$this->editPickup->getUntilTime());
    }


    public function testGetToCountry(){
        $this->assertNotEmpty($this->editPickup->getToCountry());
        $this->assertNotNull($this->editPickup->getToCountry());
        $this->assertSame("CA",$this->editPickup->getToCountry());
    }


    public function testGetInstructions(){
        $this->assertNotEmpty($this->editPickup->getInstructions());
        $this->assertNotNull($this->editPickup->getInstructions());
        $this->assertSame("Package contains glass product",$this->editPickup->getInstructions());
    }


    public function testIsCancelled(){
        $this->assertNotNull($this->editPickup->isCancelled());
        $this->assertSame(FALSE,$this->editPickup->isCancelled());
    }


    protected function setUp(){
        $response = '{
            "id": "1085704",
            "confirmation": "2929602E9CP",
            "address": {
                "name": "fls",
                "attn": "ryan",
                "address": "3767 thimens",
                "suite": "227",
                "city": "SAINT-LAURENT",
                "country": "CA",
                "state": "QC",
                "postal_code": "H4R1W4",
                "phone": "5146789056",
                "ext": "11",
                "is_commercial": "1"
            },
            "courier": "ups",
            "units": "imperial",
            "boxes": "2",
            "weight": "8",
            "location": "FrontDesk",
            "date": "2018-11-05",
            "from": "09:00:00",
            "until": "15:00:00",
            "to_country": "CA",
            "instruction": "Package contains glass product",
            "cancelled": false
        }';

        $this->editPickup = $this->getMockBuilder(Pickup::class)
                              ->setConstructorArgs([json_decode($response)])
                              ->setMethods(['__construct'])
                              ->getMock();

    }
}
