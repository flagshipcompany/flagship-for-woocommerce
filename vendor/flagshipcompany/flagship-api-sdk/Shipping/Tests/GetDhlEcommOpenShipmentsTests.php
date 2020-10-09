<?php
namespace Flagship\Shipping\Tests;

use Flagship\Shipping\Requests\GetDhlEcommOpenShipmentsRequest;
use \PHPUnit\Framework\TestCase;
use Flagship\Shipping\Collections\GetShipmentListCollection;
use Flagship\Shipping\Objects\Shipment;
use Flagship\Shipping\Exceptions\GetShipmentListException;

class GetDhlEcommOpenShipmentsTests extends TestCase{

    public function testGetById(){
        $this->assertNotNull($this->shipmentsList->getById(3372152));
        $this->assertInstanceOf(Shipment::class, $this->shipmentsList->getById(3372152));
        $this->assertSame(31.62,$this->shipmentsList->getById(3372152)->getTotal());
    }

    public function testGetByTrackingNumber(){
        $this->assertNotNull($this->shipmentsList->getByTrackingNumber("FS3372152FC0D9"));
        $this->assertInstanceOf(Shipment::class, $this->shipmentsList->getById("FS3372152FC0D9"));
        $this->assertSame("Rachitta",$this->shipmentsList->getByTrackingNumber("FS3372152FC0D9")->getReceiverName());
    }

    public function testGetByPickupId(){
        $this->assertNull($this->shipmentsList->getByPickupId(3372152));
    }

    public function testGetByStatus(){
        $this->assertNotNull($this->shipmentsList->getByStatus('dispatched'));
        $this->assertSame(4, $this->shipmentsList->getByStatus('dispatched')->count());
    }

    public function testGetBySender(){
        $this->assertNotNull($this->shipmentsList->getBySender("Reception"));
        $this->assertSame("PKY",$this->shipmentsList->getBySender("Reception")->first()->getCourierCode());
    }

    public function testGetByReceiver(){
        $this->assertNotNull($this->shipmentsList->getByReceiver("Rachitta A Dua"));
        $this->assertSame(2000.00,$this->shipmentsList->getByReceiver("Rachitta A Dua")->getInsuranceValue());
    }

    public function testGetByReference(){
        $this->assertNull($this->shipmentsList->getByReference('some reference'));
    }

    public function testGetByDate(){
        $this->assertNotNull($this->shipmentsList->getByDate("2019-12-05"));
        $this->assertInstanceOf(GetShipmentListCollection::class,$this->shipmentsList->getByDate("2019-12-05") );
    }

    public function testGetByCourier(){
        $this->assertNotNull($this->shipmentsList->getByCourier('dhlec'));
        $this->assertSame('metric',$this->shipmentsList->getByCourier('dhlec')->last()->getPackageUnits());
    }

    public function testGetByReceiverPhone(){
        $this->assertNotNull($this->shipmentsList->getByReceiverPhone('9814531195'));
        $this->assertSame('QC',$this->shipmentsList->getByReceiverPhone('9814531195')->getSenderState());
    }

    public function testBySenderPhone(){
        $this->expectException(GetShipmentListException::class);
        $this->assertNull($this->getBySenderPhone('317746883')); 
    }

    public function testGetBySenderCompany(){
        $this->assertNotNull($this->shipmentsList->getBySenderCompany("Flagship Courier Solutions"));
        $this->assertSame("dispatched", $this->shipmentsList->getBySenderCompany("Flagship Courier Solutions")->getById(3372152)->getstatus());
    }

    public function testGetByReceiverCompany(){
        $this->assertNotNull($this->shipmentsList->getByReceiverCompany("INRA"));
        $this->assertInstanceOf(GetShipmentListCollection::class, $this->shipmentsList->getByReceiverCompany("INRA"));
    }

    protected function setUp(){
        $this->getDhlEcommOpenShipmentsRequest = $this->getMockBuilder(GetDhlEcommOpenShipmentsRequest::class)
                                                    ->setConstructorArgs(['testToken','testUrl',23,'testing','1.0.11'])
                                                    ->setMethods(['execute'])
                                                    ->getMock();
        $this->openShipmentsRequest = $this->getDhlEcommOpenShipmentsRequest->execute();
        
        $response =     '[
            {
                "id": 3372182,
                "tracking_number": "FS337218247B01",
                "status": "dispatched",
                "pickup_id": null,
                "from": {
                    "name": "Flagship Courier Solutions",
                    "attn": "Reception",
                    "address": "148 Brunswick",
                    "suite": null,
                    "department": " ",
                    "city": "Pointe-Claire",
                    "country": "CA",
                    "state": "QC",
                    "postal_code": "H9R 5P9",
                    "phone": "5147390202",
                    "phone_ext": null
                },
                "to": {
                    "name": "Rachitta",
                    "attn": "Rachitta A Dua",
                    "address": "Major Shivdev singh marg",
                    "suite": "1/1 A",
                    "department": " ",
                    "is_commercial": true,
                    "city": "Ludhiana",
                    "country": "IN",
                    "state": "PB",
                    "postal_code": "141001",
                    "phone": "9814531195",
                    "phone_ext": null
                },
                "options": {
                    "insurance": {
                        "value": 2000,
                        "description": "Battle-ready lightsaber"
                    },
                    "shipping_date": "2019-12-06"
                },
                "payment": {
                    "payer": "F"
                },
                "service": {
                    "courier_code": "PKY",
                    "courier_desc": "DHL GlobalMail Packet Priority",
                    "courier_name": "dhlec",
                    "estimated_delivery_date": null
                },
                "packages": {
                    "content": "goods",
                    "units": "metric",
                    "type": "package",
                    "items": [
                        {
                            "pin": "FS337218247B01",
                            "height": 8,
                            "length": 31,
                            "width": 31,
                            "weight": 27,
                            "description": "Very nicely packed thing"
                        }
                    ]
                },
                "price": {
                    "charges": {
                        "freight": 2.88,
                        "insurance": 25
                    },
                    "adjustments": null,
                    "brokerage": null,
                    "subtotal": 27.88,
                    "total": 31.62,
                    "taxes": {
                        "gst": 1.25,
                        "qst": 2.49
                    }
                },
                "brokerage_details": null,
                "transit_details": [
                    {
                        "status": "M",
                        "last_update": null,
                        "message": null,
                        "local_tracking_number": null
                    }
                ]
            },
            {
                "id": 3372180,
                "tracking_number": "FS3372180E6120",
                "status": "dispatched",
                "pickup_id": null,
                "from": {
                    "name": "Flagship Courier Solutions",
                    "attn": "Reception",
                    "address": "148 Brunswick",
                    "suite": null,
                    "department": " ",
                    "city": "Pointe-Claire",
                    "country": "CA",
                    "state": "QC",
                    "postal_code": "H9R 5P9",
                    "phone": "5147390202",
                    "phone_ext": null
                },
                "to": {
                    "name": "INRA",
                    "attn": "Marie Martin",
                    "address": "14 rue Girardet",
                    "suite": null,
                    "department": " ",
                    "is_commercial": true,
                    "city": "Nancy",
                    "country": "FR",
                    "state": null,
                    "postal_code": "54042",
                    "phone": "383396892",
                    "phone_ext": null
                },
                "options": {
                    "insurance": {
                        "value": 2000,
                        "description": "Battle-ready lightsaber"
                    },
                    "shipping_date": "2019-12-06"
                },
                "payment": {
                    "payer": "F"
                },
                "service": {
                    "courier_code": "PKT",
                    "courier_desc": "DHL GlobalMail Packet Plus",
                    "courier_name": "dhlec",
                    "estimated_delivery_date": null
                },
                "packages": {
                    "content": "goods",
                    "units": "metric",
                    "type": "package",
                    "items": [
                        {
                            "pin": "FS3372180E6120",
                            "height": 8,
                            "length": 8,
                            "width": 8,
                            "weight": 27,
                            "description": "Very nicely packed thing"
                        }
                    ]
                },
                "price": {
                    "charges": {
                        "freight": 20.85,
                        "insurance": 25
                    },
                    "adjustments": null,
                    "brokerage": null,
                    "subtotal": 45.85,
                    "total": 49.59,
                    "taxes": {
                        "gst": 1.25,
                        "qst": 2.49
                    }
                },
                "brokerage_details": null,
                "transit_details": [
                    {
                        "status": "M",
                        "last_update": null,
                        "message": null,
                        "local_tracking_number": null
                    }
                ]
            },
            {
                "id": 3372155,
                "tracking_number": "FS337215595B8E",
                "status": "dispatched",
                "pickup_id": null,
                "from": {
                    "name": "Flagship Courier Solutions",
                    "attn": "Reception",
                    "address": "148 Brunswick",
                    "suite": null,
                    "department": " ",
                    "city": "Pointe-Claire",
                    "country": "CA",
                    "state": "QC",
                    "postal_code": "H9R 5P9",
                    "phone": "5147390202",
                    "phone_ext": null
                },
                "to": {
                    "name": "Rachitta",
                    "attn": "Rachitta A Dua",
                    "address": "Major Shivdev singh marg",
                    "suite": "1/1 A",
                    "department": " ",
                    "is_commercial": true,
                    "city": "Ludhiana",
                    "country": "IN",
                    "state": "PB",
                    "postal_code": "141001",
                    "phone": "9814531195",
                    "phone_ext": null
                },
                "options": {
                    "insurance": {
                        "value": 2000,
                        "description": "Battle-ready lightsaber"
                    },
                    "shipping_date": "2019-12-05"
                },
                "payment": {
                    "payer": "F"
                },
                "service": {
                    "courier_code": "PKY",
                    "courier_desc": "DHL GlobalMail Packet Priority",
                    "courier_name": "dhlec",
                    "estimated_delivery_date": null
                },
                "packages": {
                    "content": "goods",
                    "units": "metric",
                    "type": "package",
                    "items": [
                        {
                            "pin": "FS337215595B8E",
                            "height": 8,
                            "length": 31,
                            "width": 31,
                            "weight": 27,
                            "description": "Very nicely packed thing"
                        }
                    ]
                },
                "price": {
                    "charges": {
                        "freight": 2.88,
                        "insurance": 25
                    },
                    "adjustments": null,
                    "brokerage": null,
                    "subtotal": 27.88,
                    "total": 31.62,
                    "taxes": {
                        "gst": 1.25,
                        "qst": 2.49
                    }
                },
                "brokerage_details": null,
                "transit_details": [
                    {
                        "status": "M",
                        "last_update": null,
                        "message": null,
                        "local_tracking_number": null
                    }
                ]
            },
            {
                "id": 3372152,
                "tracking_number": "FS3372152FC0D9",
                "status": "dispatched",
                "pickup_id": null,
                "from": {
                    "name": "Flagship Courier Solutions",
                    "attn": "Reception",
                    "address": "148 Brunswick",
                    "suite": null,
                    "department": " ",
                    "city": "Pointe-Claire",
                    "country": "CA",
                    "state": "QC",
                    "postal_code": "H9R 5P9",
                    "phone": "5147390202",
                    "phone_ext": null
                },
                "to": {
                    "name": "Rachitta",
                    "attn": "Rachitta A Dua",
                    "address": "Major Shivdev singh marg",
                    "suite": "1/1 A",
                    "department": " ",
                    "is_commercial": true,
                    "city": "Ludhiana",
                    "country": "IN",
                    "state": "PB",
                    "postal_code": "141001",
                    "phone": "9814531195",
                    "phone_ext": null
                },
                "options": {
                    "insurance": {
                        "value": 2000,
                        "description": "Battle-ready lightsaber"
                    },
                    "shipping_date": "2019-12-05"
                },
                "payment": {
                    "payer": "F"
                },
                "service": {
                    "courier_code": "PKY",
                    "courier_desc": "DHL GlobalMail Packet Priority",
                    "courier_name": "dhlec",
                    "estimated_delivery_date": null
                },
                "packages": {
                    "content": "goods",
                    "units": "metric",
                    "type": "package",
                    "items": [
                        {
                            "pin": "FS3372152FC0D9",
                            "height": 8,
                            "length": 31,
                            "width": 31,
                            "weight": 27,
                            "description": "Very nicely packed thing"
                        }
                    ]
                },
                "price": {
                    "charges": {
                        "freight": 2.88,
                        "insurance": 25
                    },
                    "adjustments": null,
                    "brokerage": null,
                    "subtotal": 27.88,
                    "total": 31.62,
                    "taxes": {
                        "gst": 1.25,
                        "qst": 2.49
                    }
                },
                "brokerage_details": null,
                "transit_details": [
                    {
                        "status": "M",
                        "last_update": null,
                        "message": null,
                        "local_tracking_number": null
                    }
                ]
            },  
        ]';

        $this->shipmentsList =  new GetShipmentListCollection(json_decode($response));
    }
}
