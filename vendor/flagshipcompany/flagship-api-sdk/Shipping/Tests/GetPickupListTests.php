<?php
namespace Flagship\Shipping\Tests;

use \PHPUnit\Framework\TestCase;
use Flagship\Shipping\Requests\GetPickupListRequest;
use Flagship\Shipping\Objects\Pickup;
use Flagship\Shipping\Collections\GetPickupListCollection;
use Flagship\Shipping\Exceptions\GetPickupListException;

class GetPickupListTests extends TestCase{

    public function testGetById(){
        $this->assertNotNull($this->pickupList->getById(1276083));
        $this->assertInstanceOf(Pickup::class,$this->pickupList->getById(1276083));
        $this->assertSame("ups",$this->pickupList->getById(1276081)->getCourier());
    }

    public function testGetBySender(){
        $this->assertNotNull($this->pickupList->getBySender('Bob'));
        $this->assertInstanceOf(GetPickupListCollection::class,$this->pickupList->getBySender("customer service"));
        $this->assertSame("A1A1A1",$this->pickupList->getBySender("Bob")->first()->getPostalCode());
        $this->expectException(GetPickupListException::class);
        $this->assertNotNull($this->pickupList->getBySender('random'));
    }

    public function testGetByPhone(){
        $this->assertNotNull($this->pickupList->getByPhone("18663208383"));
        $this->assertInstanceOf(GetPickupListCollection::class,$this->pickupList->getByPhone("18663208383"));
        $this->assertSame("Reception",$this->pickupList->getByPhone("18663208383")->last()->getPickupLocation());
    }

    public function testGetByCourier(){
        $this->assertNotNull($this->pickupList->getByCourier("canpar"));
        $this->assertInstanceOf(GetPickupListCollection::class,$this->pickupList->getByCourier("ups"));
        $this->assertSame("imperial",$this->pickupList->getByCourier("canpar")->first()->getUnits());
    }

    public function testGetCommercialPickups(){
        $this->assertNotNull($this->pickupList->getCommercialPickups());
        $this->assertInstanceOf(GetPickupListCollection::class,$this->pickupList->getCommercialPickups());
        $this->assertSame(1276083,$this->pickupList->getCommercialPickups()->first()->getId());
    }

    public function testGetByDate(){
        $this->assertNotNull($this->pickupList->getByDate("2019-12-13"));
        $this->assertSame("CA",$this->pickupList->getByDate("2019-12-13")->last()->getToCountry());
        $this->expectException(GetPickupListException::class);
        $this->assertNull($this->pickupList->getByDate("2019-10-17"));
    }

    public function testGetCancelledPickups(){
        $this->expectException(GetPickupListException::class);
        $this->assertNotNull($this->pickupList->getCancelledPickups());
    }

    protected function setUp(){
        $response = '[
                {
                    "id": "1276083",
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
                },
                {
                    "id": "1276082",
                    "confirmation": "2929602E9CP",
                    "address": {
                        "name": "ACME inc.",
                        "attn": "Bob",
                        "address": "123 Main Street",
                        "suite": "227",
                        "city": "Montreal",
                        "country": "CA",
                        "state": "QC",
                        "postal_code": "A1A1A1",
                        "phone": "18663208383",
                        "ext": "211",
                        "is_commercial": "1"
                    },
                    "courier": "ups",
                    "units": "imperial",
                    "boxes": "4",
                    "weight": "6",
                    "location": "Reception",
                    "date": "2019-11-30",
                    "from": "09:00",
                    "until": "17:00",
                    "to_country": "CA",
                    "instruction": "contain glass product, etc.",
                    "cancelled": false
                },
                {
                    "id": "1276081",
                    "confirmation": "2929602E9CP",
                    "address": {
                        "name": "ACME inc.",
                        "attn": "Bob",
                        "address": "123 Main Street",
                        "suite": "227",
                        "city": "Montreal",
                        "country": "CA",
                        "state": "QC",
                        "postal_code": "A1A1A1",
                        "phone": "18663208383",
                        "ext": "211",
                        "is_commercial": "1"
                    },
                    "courier": "ups",
                    "units": "imperial",
                    "boxes": "4",
                    "weight": "6",
                    "location": "Reception",
                    "date": "2019-11-30",
                    "from": "09:00",
                    "until": "17:00",
                    "to_country": "CA",
                    "instruction": "contain glass product, etc.",
                    "cancelled": false
                },
                {
                    "id": "1276080",
                    "confirmation": "2929602E9CP",
                    "address": {
                        "name": "ACME inc.",
                        "attn": "Bob",
                        "address": "123 Main Street",
                        "suite": "227",
                        "city": "Montreal",
                        "country": "CA",
                        "state": "QC",
                        "postal_code": "A1A1A1",
                        "phone": "18663208383",
                        "ext": "211",
                        "is_commercial": "1"
                    },
                    "courier": "ups",
                    "units": "imperial",
                    "boxes": "4",
                    "weight": "6",
                    "location": "Reception",
                    "date": "2019-11-30",
                    "from": "09:00",
                    "until": "17:00",
                    "to_country": "CA",
                    "instruction": "contain glass product, etc.",
                    "cancelled": false
                },
                {
                    "id": "1276079",
                    "confirmation": "2929602E9CP",
                    "address": {
                        "name": "ACME inc.",
                        "attn": "Bob",
                        "address": "123 Main Street",
                        "suite": "227",
                        "city": "Montreal",
                        "country": "CA",
                        "state": "QC",
                        "postal_code": "A1A1A1",
                        "phone": "18663208383",
                        "ext": "211",
                        "is_commercial": "1"
                    },
                    "courier": "ups",
                    "units": "imperial",
                    "boxes": "4",
                    "weight": "6",
                    "location": "Reception",
                    "date": "2019-11-30",
                    "from": "09:00",
                    "until": "17:00",
                    "to_country": "CA",
                    "instruction": "contain glass product, etc.",
                    "cancelled": false
                }
            ]';
        $this->getPickupListRequest = $this->getMockBuilder(GetPickupListRequest::class)
            ->setConstructorArgs(['testToken','localhost','test','1.0.11'])
            ->setMethods(['execute'])
            ->getMock();
        $this->getPickupList = $this->getPickupListRequest->execute();
        $this->pickupList = new GetPickupListCollection();
        $this->pickupList->importPickups(json_decode($response));
    }
}
