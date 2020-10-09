<?php

namespace Flagship\Shipping\Tests;

use \PHPUnit\Framework\TestCase;
use Flagship\Shipping\Requests\ConfirmManifestByIdRequest;
use Flagship\Shipping\Exceptions\ConfirmManifestByIdException;
use Flagship\Shipping\Objects\Manifest;
use Flagship\Shipping\Objects\Shipment;
use Flagship\Shipping\Objects\Rate;
use Flagship\Shipping\Collections\RatesCollection;

class ConfirmManifestByIdTests extends TestCase{

    public function testGetName(){

        $this->assertNotNull($this->manifest->getName());
        $this->assertSame('MyNewManifest',$this->manifest->getName());
    }

    public function testGetStatus(){
        $this->assertNotNull($this->manifest->getStatus());
        $this->assertSame('confirmed',$this->manifest->getStatus());
    }

    public function testGetId(){
        $this->assertNotNull($this->manifest->getId());
        $this->assertSame(23,$this->manifest->getId());
    }

    public function testGetToDepotShipment(){
        $this->assertNotNull($this->manifest->getToDepotShipment());
        $this->assertSame(3372200,$this->manifest->getToDepotShipment()->getId());
        $this->assertSame("Pointe-Claire",$this->manifest->getToDepotShipment()->getSenderCity());
    }

    public function testGetShipmentIds(){
        $this->assertNotNull($this->manifest->getShipmentIds());
        $this->assertSame([
               "3372198",
               "3372199"
           ],$this->manifest->getShipmentIds());
    }

    public function testGetPriceByShipmentId(){
        $this->assertNotNull($this->manifest->getPriceByShipmentId(3372199));
        $this->assertInstanceOf(Rate::class, $this->manifest->getPriceByShipmentId(3372199));
        $this->assertSame(45.85,$this->manifest->getPriceByShipmentId(3372198)->getSubtotal());
    }

    public function testGetAllPrices(){
        $this->assertNotNull($this->manifest->getAllPrices());
        $this->assertInstanceOf(RatesCollection::class,$this->manifest->getAllPrices());
        $this->assertSame(0.74,$this->manifest->getAllPrices()->getCheapest()->getTaxesTotal());
    }

    public function testGetSubtotal(){
        $this->assertNotNull($this->manifest->getSubtotal());
        $this->assertSame(115.67,$this->manifest->getSubtotal());
    }

    public function testGetTotal(){
        $this->assertNotNull($this->manifest->getTotal());
        $this->assertSame(126.73,$this->manifest->getTotal());
    }

    public function testGetTaxesDetails(){
        $this->assertNotNull($this->manifest->getTaxesDetails());
        $this->assertSame([
            "gst"=> 1.5,
            "qst"=> 2.9800000000000004,
            "hst"=> 6.58
        ],$this->manifest->getTaxesDetails());
    }

    public function testGetTaxesTotal(){
        $this->assertNotNull($this->manifest->getTaxesTotal());
        $this->assertSame(11.06,$this->manifest->getTaxesTotal());
    }

    public function testGetToDepotId(){
        $this->assertNotNull($this->manifest->getToDepotId());
        $this->assertSame(3372200,$this->manifest->getToDepotId());
    }

    public function testGetBolNumber(){
        $this->assertNotNull($this->manifest->getBolNumber());
        $this->assertSame(41281204,$this->manifest->getBolNumber());
    }

    public function testGetShipmentsLabels(){
        $this->assertNotNull($this->manifest->getShipmentsLabels());
        $this->assertSame("https://www.flagshipcompany.com/ship/23/order-docs/a6eebaaded302b43a80811830a50348bff0ce928",$this->manifest->getShipmentsLabels());
    }

    public function testGetShipmentsThermalLabels(){
        $this->assertNotNull($this->manifest->getShipmentsThermalLabels());
        $this->assertSame("https://www.flagshipcompany.com/ship/23/order-docs/a6eebaaded302b43a80811830a50348bff0ce928?document=therm",$this->manifest->getShipmentsThermalLabels());
    }

    public function testGetManifestSummary(){
        $this->assertNotNull($this->manifest->getManifestSummary());
         $this->assertSame("https://www.flagshipcompany.com/ship/23/order-docs/a6eebaaded302b43a80811830a50348bff0ce928?document=manifest", $this->manifest->getManifestSummary());
    }

    public function testGetToDepotLabel(){
        $this->assertNotNull($this->manifest->getToDepotLabel());
        $this->assertSame("https://www.flagshipcompany.com/ship/3372200/labels/3074e7fa9637e74d999cd09ba4e7cae6804b6a14",$this->manifest->getToDepotLabel());
    }

    public function testGetToDepotThermalLabel(){
        $this->assertNotNull($this->manifest->getToDepotThermalLabel());
        $this->assertSame("https://flagshipcompany.com/ship/3372200/labels/3074e7fa9637e74d999cd09ba4e7cae6804b6a14?document=therm",$this->manifest->getToDepotThermalLabel());
    }

    protected function setUp(){

        $response = '{
            "id": "23",
            "name": "MyNewManifest",
            "status": "confirmed",
            "to_depot_id": "3372200",
            "bol_number": "41281204",
            "to_depot_shipment": {
                "id": 3372200,
                "tracking_number": "1Z0075Y00496060060",
                "status": "dispatched",
                "pickup_id": null,
                "from": {
                    "name": "Flagship Courier Solutions",
                    "attn": "Reception",
                    "address": "Brunswick Boulevard",
                    "suite": "148",
                    "department": " ",
                    "city": "Pointe-Claire",
                    "country": "CA",
                    "state": "QC",
                    "postal_code": "H9R5P9",
                    "phone": "5147390202",
                    "phone_ext": null
                },
                "to": {
                    "name": "DHL eCommerce",
                    "attn": "DHL eCommerce",
                    "address": "4-355 Admiral Blvd.",
                    "suite": null,
                    "department": " ",
                    "is_commercial": true,
                    "city": "Mississauga",
                    "country": "CA",
                    "state": "ON",
                    "postal_code": "L5T 2N1",
                    "phone": "6475887155",
                    "phone_ext": null
                },
                "options": {
                    "shipping_date": "2019-12-09"
                },
                "payment": {
                    "payer": "F"
                },
                "service": {
                    "courier_code": "65",
                    "courier_desc": "UPS Express Saver",
                    "courier_name": "ups",
                    "estimated_delivery_date": "2019-12-10 16:30:00"
                },
                "packages": {
                    "content": "goods",
                    "units": "metric",
                    "type": "package",
                    "items": [
                        {
                            "pin": "1Z0075Y00496060060",
                            "height": 31,
                            "length": 31,
                            "width": 31,
                            "weight": 1,
                            "description": "Very nicely packed thing"
                        }
                    ]
                },
                "price": {
                    "charges": {
                        "freight": 50.64
                    },
                    "adjustments": null,
                    "brokerage": null,
                    "subtotal": 50.64,
                    "total": 57.22,
                    "taxes": {
                        "hst": 6.58
                    }
                },
                "invoice_details": [],
                "brokerage_details": null,
                "documents": {
                    "regular_label": "http://192.168.188.74:3002/ship/3372200/labels/3074e7fa9637e74d999cd09ba4e7cae6804b6a14?document=reg",
                    "thermal_label": "http://192.168.188.74:3002/ship/3372200/labels/3074e7fa9637e74d999cd09ba4e7cae6804b6a14?document=therm"
                },
                "transit_details": [
                    {
                        "status": "M",
                        "last_update": null,
                        "message": null
                    }
                ]
            },
            "shipment_ids": [
                "3372198",
                "3372199"
            ],
            "prices": {
                "3372198": {
                    "price": {
                        "charges": {
                            "freight": 20.85,
                            "insurance": 25
                        },
                        "adjustments": null,
                        "brokerage": null,
                        "adjustment_descriptions": [],
                        "subtotal": 45.85,
                        "total": 49.59,
                        "taxes": {
                            "gst": 1.25,
                            "qst": 2.49
                        }
                    },
                    "service": {
                        "courier_code": "PKT",
                        "courier_desc": "DHL GlobalMail Packet Plus",
                        "courier_name": "dhlec",
                        "transit_time": null,
                        "estimated_delivery_date": null
                    }
                },
                "3372199": {
                    "price": {
                        "charges": {
                            "freight": 14.23,
                            "insurance": 4.95
                        },
                        "adjustments": null,
                        "brokerage": null,
                        "adjustment_descriptions": [],
                        "subtotal": 19.18,
                        "total": 19.92,
                        "taxes": {
                            "gst": 0.25,
                            "qst": 0.49
                        }
                    },
                    "service": {
                        "courier_code": "PKT",
                        "courier_desc": "DHL GlobalMail Packet Plus",
                        "courier_name": "dhlec",
                        "transit_time": null,
                        "estimated_delivery_date": null
                    }
                }
            },
            "totals": {
                "subtotal": 115.67,
                "total": 126.73,
                "taxes": {
                    "gst": 1.5,
                    "qst": 2.9800000000000004,
                    "hst": 6.58
                }
            },
            "documents": {
                "labels": {
                    "regular": "https://www.flagshipcompany.com/ship/23/order-docs/a6eebaaded302b43a80811830a50348bff0ce928",
                    "thermal": "https://www.flagshipcompany.com/ship/23/order-docs/a6eebaaded302b43a80811830a50348bff0ce928?document=therm"
                },
                "manifest": "https://www.flagshipcompany.com/ship/23/order-docs/a6eebaaded302b43a80811830a50348bff0ce928?document=manifest",
                "to_depot": {
                    "regular": "https://www.flagshipcompany.com/ship/3372200/labels/3074e7fa9637e74d999cd09ba4e7cae6804b6a14",
                    "thermal": "https://flagshipcompany.com/ship/3372200/labels/3074e7fa9637e74d999cd09ba4e7cae6804b6a14?document=therm"
                }
            }
        }';

        $this->confirmManifestByIdRequest = $this->getMockBuilder(ConfirmManifestByIdRequest::class)
                          ->setConstructorArgs(['YuX5juWBvBB2oE1NohOc99qiaSutOM4C7tUpigGawkA','127.0.0.1:3002',23,'testing','1.0.11'])
                          ->setMethods(['execute'])
                          ->getMock();
        $this->manifestRequest = $this->confirmManifestByIdRequest->execute();
        $this->manifest = new Manifest(json_decode($response));
    }
}
