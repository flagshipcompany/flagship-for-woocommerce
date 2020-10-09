<?php
namespace Flagship\Shipping\Tests;

use \PHPUnit\Framework\TestCase;
use Flagship\Shipping\Objects\Pickup;

class PickupTests extends TestCase{

    public function testGetId(){
        $this->assertNotNull($this->pickup->getId());
        $this->assertSame(1085501, $this->pickup->getId());
    }

    public function testGetConfirmation(){
        $this->assertNotNull($this->pickup->getConfirmation());
        $this->assertSame('00006293', $this->pickup->getConfirmation());
    }

    public function testGetFullAddress(){
        $expectedResult = [
            "name"=> "FCS",
            "attn"=> "customer service",
            "address"=> "148 brunswick boul",
            "suite"=> "56",
            "city"=> "Pointe-Claire",
            "country"=> "CA",
            "state"=> "QC",
            "postal_code"=> "H9R5P9",
            "phone"=> "1234567890",
            "ext"=> "6789",
            "is_commercial"=> "1"
        ];
        $this->assertNotEmpty($this->pickup->getFullAddress());
        $this->assertNotNull($this->pickup->getFullAddress());
        $this->assertSame($expectedResult,$this->pickup->getFullAddress());
    }

    public function testGetAddress() {
        $this->assertNotNull($this->pickup->getAddress());
        $this->assertSame('148 brunswick boul',$this->pickup->getAddress());
    }

    public function testGetName() {
        $this->assertNotNull($this->pickup->getName());
        $this->assertSame('customer service',$this->pickup->getName());
    }

    public function testGetCompany() {
        $this->assertNotNull($this->pickup->getCompany());
        $this->assertSame('FCS',$this->pickup->getCompany());
    }

    public function testGetSuite() {
        $this->assertNotNull($this->pickup->getSuite());
        $this->assertSame('56',$this->pickup->getSuite());
    }

    public function testGetCity() {
        $this->assertNotNull($this->pickup->getCity());
        $this->assertSame('Pointe-Claire',$this->pickup->getCity());
    }

    public function testGetCountry() {
        $this->assertNotNull($this->pickup->getCountry());
        $this->assertSame('CA',$this->pickup->getCountry());
    }

    public function testGetState() {
        $this->assertNotNull($this->pickup->getState());
        $this->assertSame('QC',$this->pickup->getState());
    }

    public function testGetPostalCode() {
        $this->assertNotNull($this->pickup->getPostalCode());
        $this->assertSame('H9R5P9',$this->pickup->getPostalCode());
    }

    public function testGetPhone() {
        $this->assertNotNull($this->pickup->getPhone());
        $this->assertSame('1234567890',$this->pickup->getPhone());
    }

    public function testGetPhoneExt() {
        $this->assertNotNull($this->pickup->getPhoneExt());
        $this->assertSame('6789',$this->pickup->getPhoneExt());
    }

    public function testisAddressCommercial() {
        $this->assertNotNull($this->pickup->isAddressCommercial());
        $this->assertSame(TRUE,$this->pickup->isAddressCommercial());
    }

    public function testGetCourier(){
        $this->assertNotNull($this->pickup->getCourier());
        $this->assertSame('purolator', $this->pickup->getCourier());
    }

    public function testGetBoxes(){
        $this->assertNotNull($this->pickup->getBoxes());
        $this->assertSame('1', $this->pickup->getBoxes());
    }

    public function testGetWeight(){
        $this->assertNotNull($this->pickup->getWeight());
        $this->assertSame('1', $this->pickup->getWeight());
    }

    public function testGetPickupLocation(){
        $this->assertNotNull($this->pickup->getPickupLocation());
        $this->assertSame('FrontDesk', $this->pickup->getPickupLocation());
    }

    public function testGetDate(){
        $this->assertNotNull($this->pickup->getDate());
        $this->assertSame('2018-10-30', $this->pickup->getDate());
    }

    public function testGetFromTime(){
        $this->assertNotNull($this->pickup->getFromTime());
        $this->assertSame('13:00:00', $this->pickup->getFromTime());
    }

    public function testGetUntilTime(){
        $this->assertNotNull($this->pickup->getUntilTime());
        $this->assertSame('16:00:00', $this->pickup->getUntilTime());
    }

    public function testGetToCountry(){
        $this->assertNotNull($this->pickup->getToCountry());
        $this->assertSame('CA', $this->pickup->getToCountry());
    }

    public function testGetInstructions(){
       $this->assertSame(NULL, $this->pickup->getInstructions());
    }

    public function testIsCancelled(){
        $this->assertNotNull($this->pickup->isCancelled());
        $this->assertSame(FALSE, $this->pickup->isCancelled());
    }


    protected function setUp(){
        
        $response = '{
            "id": "1085501",
            "confirmation": "00006293",
            "address": {
                "name": "FCS",
                "attn": "customer service",
                "address": "148 brunswick boul",
                "suite": "56",
                "city": "Pointe-Claire",
                "country": "CA",
                "state": "QC",
                "postal_code": "H9R5P9",
                "phone": "1234567890",
                "ext": "6789",
                "is_commercial": "1"
            },
            "courier": "purolator",
            "units": "metric",
            "boxes": "1",
            "weight": "1",
            "location": "FrontDesk",
            "date": "2018-10-30",
            "from": "13:00:00",
            "until": "16:00:00",
            "to_country": "CA",
            "instruction": null,
            "cancelled": false
        }';

        $this->pickup = $this->getMockBuilder(Pickup::class)
                          ->setConstructorArgs([json_decode($response)])
                          ->setMethods(['__construct']) 
                          ->getMock();
    }


}