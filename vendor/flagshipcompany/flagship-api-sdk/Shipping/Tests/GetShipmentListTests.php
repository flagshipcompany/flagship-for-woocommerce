<?php

namespace Flagship\Shipping\Tests;

use \PHPUnit\Framework\TestCase;
use Flagship\Shipping\Collections\GetShipmentListCollection;
use Flagship\Shipping\Exceptions\GetShipmentListException;
use Flagship\Shipping\Requests\GetShipmentListRequest;
use Flagship\Shipping\Objects\Shipment;

class GetShipmentListTests extends TestCase{

    public function testGetById(){
        $this->expectException(GetShipmentListException::class);
        $this->assertNotNull($this->shipmentList->getById(253858));
    }

    public function testGetByTrackingNumber(){
        $this->assertNotNull($this->shipmentList->getByTrackingNumber("1Z0075Y02099202915"));
        $this->assertInstanceOf(Shipment::class,$this->shipmentList->getByTrackingNumber("1Z0075Y02099202915"));
        $this->assertSame("76764464",$this->shipmentList->getByTrackingNumber("1Z0075Y02099202915")->getPickupId());
    }

    public function testGetByStatus(){
        $this->assertNotNull($this->shipmentList->getByStatus('prequoted'));
        $this->assertInstanceOf(GetShipmentListCollection::class,$this->shipmentList->getByStatus('dispatched'));
    }

    public function testGetByPickupId(){
        $this->assertNotNull($this->shipmentList->getByPickupId(76764464));
        $this->assertInstanceOf(Shipment::class,$this->shipmentList->getByPickupId(76764464));
        $this->assertSame(1500.00,$this->shipmentList->getByPickupId(547654646)->getInsuranceValue());
    }

    public function testGetBySender(){
        $this->assertNotNull($this->shipmentList->getBySender("Customer Service"));
        $this->assertInstanceOf(GetShipmentListCollection::class,$this->shipmentList->getBySender("Customer Service"));
        $this->assertSame("11",$this->shipmentList->getBySender("Customer Service")->first()->getCourierCode());
    }

    public function testGetByReceiver(){
        $this->assertNotNull($this->shipmentList->getByReceiver("Corey Leonard"));
        $this->assertInstanceOf(GetShipmentListCollection::class,$this->shipmentList->getByReceiver("Corey Leonard"));
        $this->assertSame("My Precious",$this->shipmentList->getByReceiver("Corey Leonard")->last()->getInsuranceDescription());
    }

    public function testGetByReference(){
        $this->assertNotNull($this->shipmentList->getByReference("apitests repository reference"));
        $this->assertInstanceOf(GetShipmentListCollection::class,$this->shipmentList->getByReference("apitests repository reference"));
    }

    public function testGetByDate(){
        $this->assertNotNull($this->shipmentList->getByDate("2018-10-22"));
        $this->assertInstanceOf(GetShipmentListCollection::class,$this->shipmentList->getByDate("2018-10-22"));
        $this->assertSame("4985 Hickmore",$this->shipmentList->getByDate("2018-10-29")->first()->getReceiverAddress());
    }

    public function testGetByCourier(){
        $this->assertNotNull($this->shipmentList->getByCourier("UPS"));
        $this->assertInstanceOf(GetShipmentListCollection::class, $this->shipmentList->getByCourier("Purolator"));
        $this->expectException(GetShipmentListException::class);
        $this->assertNull($this->shipmentList->getByCourier("random"));
    }

    public function testGetBySenderPhone(){
        $this->assertNotNull($this->shipmentList->getBySenderPhone("18663208383"));
        $this->assertInstanceOf(GetShipmentListCollection::class, $this->shipmentList->getBySenderPhone("18663208383"));
        $this->assertSame(2950182,$this->shipmentList->getBySenderPhone("5147390202")->first()->getId());
    }

    public function testGetByReceiverPhone(){
        $this->assertNotNull($this->shipmentList->getByReceiverPhone("1234567890"));
        $this->assertInstanceOf(GetShipmentListCollection::class,$this->shipmentList->getByReceiverPhone("1234567890"));
        $this->assertSame(2,$this->shipmentList->getByReceiverPhone("1234567890")->count());
    }

    public function testGetBySenderCompany(){
        $this->assertNotNull($this->shipmentList->getBySenderCompany("WooComm"));
        $this->assertInstanceOf(GetShipmentListCollection::class, $this->shipmentList->getBySenderCompany("woocomm"));
        $this->assertSame("dispatched", $this->shipmentList->getBySenderCompany("Flagship courier solutions")->first()->getStatus());
    }

    public function testGetByReceiverCompany(){
        $this->assertNotNull($this->shipmentList->getByReceiverCompany("Flagship Courier Solutions"));
        $this->assertInstanceOf(GetShipmentListCollection::class,$this->shipmentList->getByReceiverCompany("4 Designs"));
        $this->assertSame("SAINT-LAURENT",$this->shipmentList->getByReceiverCompany("4 Designs")->first()->getReceiverCity());
    }

    protected function setUp(){
         $response = '[
            {
                "id": 2950191,
                "tracking_number": "1Z0075Y02099202915",
                "status": "dispatched",
                "pickup_id": "76764464",
                "from": {
                    "name": "FlagShip Courier Solutions",
                    "attn": "Customer Service",
                    "address": "148 Brunswick",
                    "suite": null,
                    "department": " ",
                    "city": "Pointe-Claire",
                    "country": "CA",
                    "state": "QC",
                    "postal_code": "H9R 5P9",
                    "phone": "18663208383",
                    "phone_ext": null
                },
                "to": {
                    "name": "4 Designs",
                    "attn": "Helene Sachdeva",
                    "address": "4985 Hickmore",
                    "suite": null,
                    "department": " ",
                    "is_commercial": true,
                    "city": "SAINT-LAURENT",
                    "country": "CA",
                    "state": "QC",
                    "postal_code": "H4T 1J5",
                    "phone": "5147724712",
                    "phone_ext": null
                },
                "options": {
                    "insurance": {
                        "value": 1500,
                        "description": "My Precious"
                    },
                    "reference": "Shipment reference here",
                    "driver_instructions": "test",
                    "shipping_date": "2018-10-29"
                },
                "payment": {
                    "payer": "F"
                },
                "service": {
                    "courier_code": "11",
                    "courier_desc": "UPS Standard",
                    "courier_name": "ups",
                    "estimated_delivery_date": "2018-10-30 23:30:00"
                },
                "packages": {
                    "content": "goods",
                    "units": "imperial",
                    "type": "package",
                    "items": [
                        {
                            "pin": "1Z0075Y02099202915",
                            "height": "15.00",
                            "length": "7.00",
                            "width": "22.00",
                            "weight": "10.00",
                            "description": null
                        }
                    ]
                },
                "price": {
                    "charges": {
                        "freight": 24.44,
                        "insurance": 18.75
                    },
                    "adjustments": null,
                    "brokerage": null,
                    "subtotal": 43.19,
                    "total": 49.66,
                    "taxes": {
                        "gst": 2.16,
                        "qst": 4.31
                    }
                },
                "brokerage_details": null,
                "documents": {
                    "regular_label": "https://flagshipcompany.com/ship/2950191/labels/b673d46530c04b0920f9b3d3f800c6c247be5232?document=reg",
                    "thermal_label": "https://flagshipcompany.com/ship/2950191/labels/b673d46530c04b0920f9b3d3f800c6c247be5232?document=therm"
                },
                "transit_details": [
                    {
                        "status": "M",
                        "last_update": null,
                        "message": null
                    }
                ]
            },
            {
                "id": 2950188,
                "tracking_number": null,
                "status": "prequoted",
                "pickup_id": "547654646",
                "from": {
                    "name": "FlagShip Courier Solutions",
                    "attn": "Customer Service",
                    "address": "148 Brunswick",
                    "suite": null,
                    "department": " ",
                    "city": "Pointe-Claire",
                    "country": "CA",
                    "state": "QC",
                    "postal_code": "H9R 5P9",
                    "phone": "18663208383",
                    "phone_ext": null
                },
                "to": {
                    "name": "4 Designs",
                    "attn": "Helene Sachdeva",
                    "address": "4985 Hickmore",
                    "suite": null,
                    "department": " ",
                    "is_commercial": true,
                    "city": "SAINT-LAURENT",
                    "country": "CA",
                    "state": "QC",
                    "postal_code": "H4T 1J5",
                    "phone": "5147724712",
                    "phone_ext": null
                },
                "options": {
                    "insurance": {
                        "value": 1500,
                        "description": "My Precious"
                    },
                    "reference": "Shipment reference here",
                    "driver_instructions": "test",
                    "shipping_date": "2018-10-29"
                },
                "payment": {
                    "payer": "F"
                },
                "service": {
                    "courier_code": "PurolatorGround",
                    "courier_desc": "Purolator Ground",
                    "courier_name": "purolator",
                    "estimated_delivery_date": null
                },
                "packages": {
                    "content": "goods",
                    "units": "imperial",
                    "type": "package",
                    "items": [
                        {
                            "pin": null,
                            "height": "15.00",
                            "length": "7.00",
                            "width": "22.00",
                            "weight": "10.00",
                            "description": null
                        }
                    ]
                },
                "price": {
                    "charges": [],
                    "adjustments": null,
                    "brokerage": null,
                    "subtotal": 0,
                    "total": 0,
                    "taxes": null
                },
                "brokerage_details": null
            },
            {
                "id": 2950187,
                "tracking_number": null,
                "status": "prequoted",
                "pickup_id": "7867868752",
                "from": {
                    "name": "FlagShip Courier Solutions",
                    "attn": "Customer Service",
                    "address": "148 Brunswick",
                    "suite": null,
                    "department": " ",
                    "city": "Pointe-Claire",
                    "country": "CA",
                    "state": "QC",
                    "postal_code": "H9R 5P9",
                    "phone": "18663208383",
                    "phone_ext": null
                },
                "to": {
                    "name": "4 Designs",
                    "attn": "Helene Sachdeva",
                    "address": "4985 Hickmore",
                    "suite": null,
                    "department": " ",
                    "is_commercial": true,
                    "city": "SAINT-LAURENT",
                    "country": "CA",
                    "state": "QC",
                    "postal_code": "H4T 1J5",
                    "phone": "5147724712",
                    "phone_ext": null
                },
                "options": {
                    "insurance": {
                        "value": 1500,
                        "description": "My Precious"
                    },
                    "reference": "Shipment reference here",
                    "driver_instructions": "test",
                    "shipping_date": "2018-10-29"
                },
                "payment": {
                    "payer": "F"
                },
                "service": {
                    "courier_code": "PurolatorGround",
                    "courier_desc": "Purolator Ground",
                    "courier_name": "purolator",
                    "estimated_delivery_date": null
                },
                "packages": {
                    "content": "goods",
                    "units": "imperial",
                    "type": "package",
                    "items": [
                        {
                            "pin": null,
                            "height": "15.00",
                            "length": "7.00",
                            "width": "22.00",
                            "weight": "10.00",
                            "description": null
                        }
                    ]
                },
                "price": {
                    "charges": [],
                    "adjustments": null,
                    "brokerage": null,
                    "subtotal": 0,
                    "total": 0,
                    "taxes": null
                },
                "brokerage_details": null
            },
            {
                "id": 2950186,
                "tracking_number": "1Z0075Y02095098715",
                "status": "dispatched",
                "pickup_id": null,
                "from": {
                    "name": "FlagShip Courier Solutions",
                    "attn": "Customer Service",
                    "address": "148 Brunswick",
                    "suite": null,
                    "department": " ",
                    "city": "Pointe-Claire",
                    "country": "CA",
                    "state": "QC",
                    "postal_code": "H9R 5P9",
                    "phone": "18663208383",
                    "phone_ext": null
                },
                "to": {
                    "name": "4 Designs",
                    "attn": "Helene Sachdeva",
                    "address": "4985 Hickmore",
                    "suite": null,
                    "department": " ",
                    "is_commercial": true,
                    "city": "SAINT-LAURENT",
                    "country": "CA",
                    "state": "QC",
                    "postal_code": "H4T 1J5",
                    "phone": "5147724712",
                    "phone_ext": null
                },
                "options": {
                    "insurance": {
                        "value": 1500,
                        "description": "My Precious"
                    },
                    "reference": "Shipment reference here",
                    "driver_instructions": "test",
                    "shipping_date": "2018-10-22"
                },
                "payment": {
                    "payer": "F"
                },
                "service": {
                    "courier_code": "11",
                    "courier_desc": "UPS Standard",
                    "courier_name": "ups",
                    "estimated_delivery_date": "2018-10-23 23:30:00"
                },
                "packages": {
                    "content": "goods",
                    "units": "imperial",
                    "type": "package",
                    "items": [
                        {
                            "pin": "1Z0075Y02095098715",
                            "height": "15.00",
                            "length": "7.00",
                            "width": "22.00",
                            "weight": "10.00",
                            "description": null
                        }
                    ]
                },
                "price": {
                    "charges": {
                        "freight": 24.33,
                        "insurance": 18.75
                    },
                    "adjustments": null,
                    "brokerage": null,
                    "subtotal": 43.08,
                    "total": 49.54,
                    "taxes": {
                        "gst": 2.16,
                        "qst": 4.3
                    }
                },
                "brokerage_details": null,
                "documents": {
                    "regular_label": "https://flagshipcompany.com/ship/2950186/labels/8dc9660587bb3534379ce699a9722d435e4ffd3f?document=reg",
                    "thermal_label": "https://flagshipcompany.com/ship/2950186/labels/8dc9660587bb3534379ce699a9722d435e4ffd3f?document=therm"
                },
                "transit_details": [
                    {
                        "status": "M",
                        "last_update": null,
                        "message": null
                    }
                ]
            },
            {
                "id": 2950185,
                "tracking_number": "1Z0075Y09197504308",
                "status": "dispatched",
                "pickup_id": null,
                "from": {
                    "name": "BigSigns.com",
                    "attn": "Corey Leonard",
                    "address": "22 South Harbor Drive",
                    "suite": "101",
                    "department": " ",
                    "city": "GRAND HAVEN",
                    "country": "US",
                    "state": "MI",
                    "postal_code": "49417",
                    "phone": "8007907611",
                    "phone_ext": null
                },
                "to": {
                    "name": "Flagship Courier Solutions",
                    "attn": "Reception",
                    "address": "3767 Thimens",
                    "suite": "227",
                    "department": " ",
                    "is_commercial": true,
                    "city": "SAINT-LAURENT",
                    "country": "CA",
                    "state": "QC",
                    "postal_code": "H4R1W4",
                    "phone": "5147390202",
                    "phone_ext": null
                },
                "options": {
                    "reference": "shipment reference here",
                    "shipping_date": "2018-10-22"
                },
                "payment": {
                    "payer": "F"
                },
                "service": {
                    "courier_code": "11",
                    "courier_desc": "UPS Standard",
                    "courier_name": "ups",
                    "estimated_delivery_date": "2018-10-25 23:30:00"
                },
                "packages": {
                    "content": "goods",
                    "units": "imperial",
                    "type": "package",
                    "items": [
                        {
                            "pin": "1Z0075Y09197504308",
                            "height": "11.00",
                            "length": "11.00",
                            "width": "11.00",
                            "weight": "11.00",
                            "description": "Very nicely packed thing"
                        }
                    ]
                },
                "price": {
                    "charges": {
                        "freight": 34.66
                    },
                    "adjustments": null,
                    "brokerage": null,
                    "subtotal": 34.66,
                    "total": 34.66,
                    "taxes": null
                },
                "brokerage_details": null,
                "documents": {
                    "regular_label": "https://flagshipcompany.com/ship/2950185/labels/8a5e9f19b93f299a6dc9e05f96502152af558ac1?document=reg",
                    "thermal_label": "https://flagshipcompany.com/ship/2950185/labels/8a5e9f19b93f299a6dc9e05f96502152af558ac1?document=therm",
                    "commercial_invoice": "https://flagshipcompany.com/ship/2950185/labels/8a5e9f19b93f299a6dc9e05f96502152af558ac1?document=statement"
                },
                "transit_details": [
                    {
                        "status": "M",
                        "last_update": null,
                        "message": null
                    }
                ]
            },
            {
                "id": 2950182,
                "tracking_number": "329022023355",
                "status": "dispatched",
                "pickup_id": null,
                "from": {
                    "name": "Flagship Courier Solutions",
                    "attn": "Reception",
                    "address": "3767 Thimens",
                    "suite": "227",
                    "department": " ",
                    "city": "SAINT-LAURENT",
                    "country": "CA",
                    "state": "QC",
                    "postal_code": "H4R1W4",
                    "phone": "5147390202",
                    "phone_ext": null
                },
                "to": {
                    "name": "Contac",
                    "attn": "Corey Leonard",
                    "address": "6749 Dennett Pl",
                    "suite": "101",
                    "department": " ",
                    "is_commercial": true,
                    "city": "Delta",
                    "country": "CA",
                    "state": "BC",
                    "postal_code": "V4G1N4",
                    "phone": "8007907611",
                    "phone_ext": null
                },
                "options": {
                    "insurance": {
                        "value": 101,
                        "description": "My Precious"
                    },
                    "cod": {
                        "method": "check",
                        "payable_to": "COD PayableTo",
                        "receiver_phone": "5678567898",
                        "amount": 99.99,
                        "currency": "CAD"
                    },
                    "reference": "apitests repository reference",
                    "shipping_date": "2018-10-22"
                },
                "payment": {
                    "payer": "F"
                },
                "service": {
                    "courier_code": "PurolatorExpress",
                    "courier_desc": "Purolator Express",
                    "courier_name": "purolator",
                    "estimated_delivery_date": "2018-10-23 00:00:00"
                },
                "packages": {
                    "content": "goods",
                    "units": "imperial",
                    "type": "package",
                    "items": [
                        {
                            "pin": "329022023355",
                            "height": "11.00",
                            "length": "11.00",
                            "width": "11.00",
                            "weight": "11.00",
                            "description": "Very nicely packed thing"
                        },
                        {
                            "pin": "329022023363",
                            "height": "11.00",
                            "length": "11.00",
                            "width": "11.00",
                            "weight": "11.00",
                            "description": "Very nicely packed thing"
                        }
                    ]
                },
                "price": {
                    "charges": {
                        "freight": 133.83,
                        "regular_cod": 15,
                        "fuel_surcharge": 17.68,
                        "insurance": 4.95
                    },
                    "adjustments": null,
                    "brokerage": null,
                    "subtotal": 171.46,
                    "total": 180.52,
                    "taxes": {
                        "gst": 8.57,
                        "qst": 0.49
                    }
                },
                "brokerage_details": null,
                "documents": {
                    "regular_label": "https://flagshipcompany.com/ship/2950182/labels/430390e9cfbf32d3c9156ae82354940145b8d078?document=reg",
                    "thermal_label": "https://flagshipcompany.com/ship/2950182/labels/430390e9cfbf32d3c9156ae82354940145b8d078?document=therm"
                },
                "transit_details": [
                    {
                        "status": "M",
                        "last_update": null,
                        "message": null
                    }
                ]
            },
            {
                "id": 2950180,
                "tracking_number": null,
                "status": "prequoted",
                "pickup_id": null,
                "from": {
                    "name": "WooComm",
                    "attn": "WooComm",
                    "address": "148 Boul. Brunswick",
                    "suite": null,
                    "department": " ",
                    "city": "Pointe Claire",
                    "country": "CA",
                    "state": "QC",
                    "postal_code": "H9R 5P9",
                    "phone": "5147390202",
                    "phone_ext": null
                },
                "to": {
                    "name": "gfhgfhgfh",
                    "attn": "djhasdj fhgfhgf",
                    "address": "foo",
                    "suite": "bar",
                    "department": " ",
                    "is_commercial": false,
                    "city": "hgfhgfhgfh",
                    "country": "CA",
                    "state": "QC",
                    "postal_code": "H6J 8K4",
                    "phone": "1234567890",
                    "phone_ext": null
                },
                "options": {
                    "signature_required": "true",
                    "reference": "WC Order# 184",
                    "shipping_date": "2018-10-15"
                },
                "payment": {
                    "payer": "F"
                },
                "service": {
                    "courier_code": null,
                    "courier_desc": null,
                    "courier_name": "N/A",
                    "estimated_delivery_date": null
                },
                "packages": {
                    "content": "goods",
                    "units": "metric",
                    "type": "package",
                    "items": [
                        {
                            "pin": null,
                            "height": 1,
                            "length": 1,
                            "width": 1,
                            "weight": 1,
                            "description": null
                        }
                    ]
                },
                "price": {
                    "charges": [],
                    "adjustments": null,
                    "brokerage": null,
                    "subtotal": 0,
                    "total": 0,
                    "taxes": null
                },
                "brokerage_details": null
            },
            {
                "id": 2950178,
                "tracking_number": null,
                "status": "prequoted",
                "pickup_id": null,
                "from": {
                    "name": "WooComm",
                    "attn": "WooComm",
                    "address": "148 Boul. Brunswick",
                    "suite": null,
                    "department": " ",
                    "city": "Pointe Claire",
                    "country": "CA",
                    "state": "QC",
                    "postal_code": "H9R 5P9",
                    "phone": "5147390202",
                    "phone_ext": null
                },
                "to": {
                    "name": "shakespeare",
                    "attn": "marc antony",
                    "address": "the senate",
                    "suite": "91",
                    "department": " ",
                    "is_commercial": false,
                    "city": "Montreal",
                    "country": "CA",
                    "state": "QC",
                    "postal_code": "H8Y 2T6",
                    "phone": "4567890123",
                    "phone_ext": null
                },
                "options": {
                    "signature_required": "true",
                    "reference": "WC Order# 183",
                    "shipping_date": "2018-10-15"
                },
                "payment": {
                    "payer": "F"
                },
                "service": {
                    "courier_code": null,
                    "courier_desc": null,
                    "courier_name": "N/A",
                    "estimated_delivery_date": null
                },
                "packages": {
                    "content": "goods",
                    "units": "metric",
                    "type": "package",
                    "items": [
                        {
                            "pin": null,
                            "height": 1,
                            "length": 1,
                            "width": 1,
                            "weight": 1,
                            "description": null
                        }
                    ]
                },
                "price": {
                    "charges": [],
                    "adjustments": null,
                    "brokerage": null,
                    "subtotal": 0,
                    "total": 0,
                    "taxes": null
                },
                "brokerage_details": null
            },
            {
                "id": 2950177,
                "tracking_number": null,
                "status": "prequoted",
                "pickup_id": null,
                "from": {
                    "name": "WooComm",
                    "attn": "WooComm",
                    "address": "148 Boul. Brunswick",
                    "suite": null,
                    "department": " ",
                    "city": "Pointe Claire",
                    "country": "CA",
                    "state": "QC",
                    "postal_code": "H9R 5P9",
                    "phone": "5147390202",
                    "phone_ext": null
                },
                "to": {
                    "name": "julius caesar",
                    "attn": "julius caesar",
                    "address": "62",
                    "suite": "the capitol",
                    "department": " ",
                    "is_commercial": false,
                    "city": "Montreal",
                    "country": "CA",
                    "state": "QC",
                    "postal_code": "H8Y 2T3",
                    "phone": "1234567890",
                    "phone_ext": null
                },
                "options": {
                    "signature_required": "true",
                    "reference": "WC Order# 182",
                    "shipping_date": "2018-10-15"
                },
                "payment": {
                    "payer": "F"
                },
                "service": {
                    "courier_code": null,
                    "courier_desc": null,
                    "courier_name": "N/A",
                    "estimated_delivery_date": null
                },
                "packages": {
                    "content": "goods",
                    "units": "metric",
                    "type": "package",
                    "items": [
                        {
                            "pin": null,
                            "height": 1,
                            "length": 1,
                            "width": 1,
                            "weight": 1,
                            "description": null
                        }
                    ]
                },
                "price": {
                    "charges": [],
                    "adjustments": null,
                    "brokerage": null,
                    "subtotal": 0,
                    "total": 0,
                    "taxes": null
                },
                "brokerage_details": null
            },
            {
                "id": 2950175,
                "tracking_number": null,
                "status": "prequoted",
                "pickup_id": null,
                "from": {
                    "name": "WooComm",
                    "attn": "WooComm",
                    "address": "148 Boul. Brunswick",
                    "suite": null,
                    "department": " ",
                    "city": "Pointe Claire",
                    "country": "CA",
                    "state": "QC",
                    "postal_code": "H9R 5P9",
                    "phone": "5147390202",
                    "phone_ext": null
                },
                "to": {
                    "name": "phoenix",
                    "attn": "phoenix ty",
                    "address": "phoenix",
                    "suite": "phoenix",
                    "department": " ",
                    "is_commercial": false,
                    "city": "phoenix",
                    "country": "CA",
                    "state": "QC",
                    "postal_code": "g5j 8j5",
                    "phone": "765645665",
                    "phone_ext": null
                },
                "options": {
                    "signature_required": "true",
                    "reference": "WC Order# 180",
                    "shipping_date": "2018-10-15"
                },
                "payment": {
                    "payer": "F"
                },
                "service": {
                    "courier_code": null,
                    "courier_desc": null,
                    "courier_name": "N/A",
                    "estimated_delivery_date": null
                },
                "packages": {
                    "content": "goods",
                    "units": "metric",
                    "type": "package",
                    "items": [
                        {
                            "pin": null,
                            "height": 1,
                            "length": 1,
                            "width": 1,
                            "weight": 1,
                            "description": null
                        }
                    ]
                },
                "price": {
                    "charges": [],
                    "adjustments": null,
                    "brokerage": null,
                    "subtotal": 0,
                    "total": 0,
                    "taxes": null
                },
                "brokerage_details": null
            },
            {
                "id": 2950174,
                "tracking_number": null,
                "status": "prequoted",
                "pickup_id": null,
                "from": {
                    "name": "WooComm",
                    "attn": "WooComm",
                    "address": "148 Boul. Brunswick",
                    "suite": null,
                    "department": " ",
                    "city": "Pointe Claire",
                    "country": "CA",
                    "state": "QC",
                    "postal_code": "H9R 5P9",
                    "phone": "5147390202",
                    "phone_ext": null
                },
                "to": {
                    "name": "hghjgb",
                    "attn": "asdghjgb",
                    "address": "jhdgsjhasdjhsakjhkj",
                    "suite": "jdskjdhjkashdkj",
                    "department": " ",
                    "is_commercial": false,
                    "city": "hhfhgdhgdgh",
                    "country": "CA",
                    "state": "QC",
                    "postal_code": "h8y 2t3",
                    "phone": "34354353543",
                    "phone_ext": null
                },
                "options": {
                    "signature_required": "true",
                    "reference": "WC Order# 168",
                    "shipping_date": "2018-10-15"
                },
                "payment": {
                    "payer": "F"
                },
                "service": {
                    "courier_code": null,
                    "courier_desc": null,
                    "courier_name": "N/A",
                    "estimated_delivery_date": null
                },
                "packages": {
                    "content": "goods",
                    "units": "metric",
                    "type": "package",
                    "items": [
                        {
                            "pin": null,
                            "height": 1,
                            "length": 1,
                            "width": 1,
                            "weight": 1,
                            "description": null
                        }
                    ]
                },
                "price": {
                    "charges": [],
                    "adjustments": null,
                    "brokerage": null,
                    "subtotal": 0,
                    "total": 0,
                    "taxes": null
                },
                "brokerage_details": null
            },
            {
                "id": 2950172,
                "tracking_number": null,
                "status": "prequoted",
                "pickup_id": null,
                "from": {
                    "name": "WooComm",
                    "attn": "WooComm",
                    "address": "148 Boul. Brunswick",
                    "suite": null,
                    "department": " ",
                    "city": "Pointe Claire",
                    "country": "CA",
                    "state": "QC",
                    "postal_code": "H9R 5P9",
                    "phone": "5147390202",
                    "phone_ext": null
                },
                "to": {
                    "name": "ghfhgf",
                    "attn": "gghfhgfhg hgfhgf",
                    "address": "gdhgf",
                    "suite": "ghdhg",
                    "department": " ",
                    "is_commercial": false,
                    "city": "gfhgf",
                    "country": "CA",
                    "state": "QC",
                    "postal_code": "g5j 8j5",
                    "phone": "765645665",
                    "phone_ext": null
                },
                "options": {
                    "signature_required": "true",
                    "reference": "WC Order# 180",
                    "shipping_date": "2018-10-15"
                },
                "payment": {
                    "payer": "F"
                },
                "service": {
                    "courier_code": null,
                    "courier_desc": null,
                    "courier_name": "N/A",
                    "estimated_delivery_date": null
                },
                "packages": {
                    "content": "goods",
                    "units": "metric",
                    "type": "package",
                    "items": [
                        {
                            "pin": null,
                            "height": 1,
                            "length": 1,
                            "width": 1,
                            "weight": 1,
                            "description": null
                        }
                    ]
                },
                "price": {
                    "charges": [],
                    "adjustments": null,
                    "brokerage": null,
                    "subtotal": 0,
                    "total": 0,
                    "taxes": null
                },
                "brokerage_details": null
            }
        ]';
       
        $this->getShipmentListRequest = $this->getMockBuilder(GetShipmentListRequest::class)
            ->setConstructorArgs(['testToken','localhost','test','1.0.11'])
            ->setMethods(['execute'])
            ->getMock();
        $this->shipmentListRequest = $this->getShipmentListRequest->execute();
        $this->shipmentList = new GetShipmentListCollection();
        $this->shipmentList->importShipments(json_decode($response));

    }
}