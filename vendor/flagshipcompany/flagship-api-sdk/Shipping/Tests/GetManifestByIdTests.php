<?php

namespace Flagship\Shipping\Tests;

use Flagship\Shipping\Requests\GetManifestByIdRequest;
use Flagship\Shipping\Exceptions\ManifestException;
use Flagship\Shipping\Objects\Manifest;
use Flagship\Shipping\Objects\Shipment;
use Flagship\Shipping\Objects\Rate;
use Flagship\Shipping\Collections\RatesCollection;
use \PHPUnit\Framework\TestCase;

class GetManifestByIdTests extends TestCase{

    public function testGetName(){
        $this->assertNotNull($this->manifest->getName());
        $this->assertSame('completeManifest',$this->manifest->getName());
    }

    public function testGetStatus(){
        $this->assertNotNull($this->manifest->getStatus());
        $this->assertSame('confirmed',$this->manifest->getStatus());
    }

    public function testGetId(){
        $this->assertNotNull($this->manifest->getId());
        $this->assertSame(20,$this->manifest->getId());
    }

    public function testGetToDepotShipment(){
        $this->assertNotNull($this->manifest->getTodepotShipment());
        $this->assertInstanceOf(Shipment::class, $this->manifest->getToDepotShipment());
    }

    public function testGetShipmentIds(){
        $this->assertNotNull($this->manifest->getShipmentIds());
        $this->assertSame([
            "3372192",
            "3372193",
            "3372194"
        ],$this->manifest->getShipmentIds());
    }

    public function testGetPriceByShipmentId(){
        $this->assertNotNull($this->manifest->getPriceByShipmentId(3372193));
        $this->assertInstanceOf(Rate::class, $this->manifest->getPriceByShipmentId(3372193));
        $this->assertSame(19.18,$this->manifest->getPriceByShipmentId(3372193)->getSubtotal());
    }

    public function testGetAllPrices(){
        $this->assertNotNull($this->manifest->getAllPrices());
        $this->assertInstanceOf(RatesCollection::class, $this->manifest->getAllPrices());
        $this->assertSame(3.74,$this->manifest->getAllPrices()->last()->getTaxesTotal());
    }

    public function testGetSubtotal(){
        $this->assertNotNull($this->manifest->getSubtotal());
        $this->assertSame(143.34,$this->manifest->getSubtotal());
    }

    public function testGetTotal(){
        $this->assertNotNull($this->manifest->getTotal());
        $this->assertSame(158.12,$this->manifest->getTotal());
    }

    public function testGetTaxesDetails(){
        $this->assertNotNull($this->manifest->getTaxesDetails());
        $this->assertSame([
            "gst"=> 2.75,
            "qst"=> 5.470000000000001,
            "hst"=> 6.56
        ],$this->manifest->getTaxesDetails());
    }

    public function testGetTaxesTotal(){
        $this->assertNotNull($this->manifest->getTaxesTotal());
        $this->assertSame(14.78,$this->manifest->getTaxesTotal());
    }

    public function testGetToDepotId(){
        $this->assertNotNull($this->manifest->getToDepotId());
        $this->assertSame(3372195,$this->manifest->getToDepotId());
    }

    public function testGetBolNumber(){
        $this->assertNotNull($this->manifest->getBolNumber());
        $this->assertSame(43998674,$this->manifest->getBolNumber());
    }

    public function testGetShipmentsLabels(){
        $this->assertNotNull($this->manifest->getShipmentsLabels());
        $this->assertSame("https://www.flagshipcompany.com/ship/20/order-docs/bbb02a8e17ada34a991c329da372f0a77debae9a",$this->manifest->getShipmentsLabels());
    }

    public function testGetShipmentsThermalLabels(){
        $this->assertNotNull($this->manifest->getShipmentsThermalLabels());
        $this->assertSame("https://www.flagshipcompany.com/ship/20/order-docs/bbb02a8e17ada34a991c329da372f0a77debae9a?document=therm",$this->manifest->getShipmentsThermalLabels());
    }

    public function testGetManifestSummary(){
        $this->assertNotNull($this->manifest->getManifestSummary());
        $this->assertSame( "https://www.flagshipcompany.com/ship/20/order-docs/bbb02a8e17ada34a991c329da372f0a77debae9a?document=manifest",$this->manifest->getManifestSummary());
    }

    public function testGetToDepotLabel(){
        $this->assertNotNull($this->manifest->getToDepotLabel());
        $this->assertSame("https://www.flagshipcompany.com/ship/3372195/labels/e5a146e501b543fd6de1c7289ce511f78c6d960c",$this->manifest->getToDepotLabel());
    }

    public function testGetToDepotThermalLabel(){
        $this->assertNotNull($this->manifest->getToDepotThermalLabel());
        $this->assertSame("https://www.flagshipcompany.com/ship/3372195/labels/e5a146e501b543fd6de1c7289ce511f78c6d960c?document=therm",$this->manifest->getToDepotThermalLabel());
    }


    protected function setUp(){

        $response = '{
            "id": "20",
            "name": "completeManifest",
            "status": "confirmed",
            "to_depot_id": "3372195",
            "bol_number": "43998674",
            "to_depot_shipment": {
                "id": 3372195,
                "tracking_number": "1Z0075Y00496929631",
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
                    "shipping_date": "2019-12-06"
                },
                "payment": {
                    "payer": "F"
                },
                "service": {
                    "courier_code": "65",
                    "courier_desc": "UPS Express Saver",
                    "courier_name": "ups",
                    "estimated_delivery_date": "2019-12-09 16:30:00"
                },
                "packages": {
                    "content": "goods",
                    "units": "metric",
                    "type": "package",
                    "items": [
                        {
                            "pin": "1Z0075Y00496929631",
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
                        "freight": 50.43
                    },
                    "adjustments": null,
                    "brokerage": null,
                    "subtotal": 50.43,
                    "total": 56.99,
                    "taxes": {
                        "hst": 6.56
                    }
                },
                "invoice_details": [],
                "brokerage_details": null,
                "documents": {
                    "regular_label": "https://www.flagshipcompany.com/ship/3372195/labels/e5a146e501b543fd6de1c7289ce511f78c6d960c?document=reg",
                    "thermal_label": "https://www.flagshipcompany.com/ship/3372195/labels/e5a146e501b543fd6de1c7289ce511f78c6d960c?document=therm"
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
                "3372192",
                "3372193",
                "3372194"
            ],
            "prices": {
                "3372192": {
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
                "3372193": {
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
                },
                "3372194": {
                    "price": {
                        "charges": {
                            "freight": 2.88,
                            "insurance": 25
                        },
                        "adjustments": null,
                        "brokerage": null,
                        "adjustment_descriptions": [],
                        "subtotal": 27.88,
                        "total": 31.62,
                        "taxes": {
                            "gst": 1.25,
                            "qst": 2.49
                        }
                    },
                    "service": {
                        "courier_code": "PKY",
                        "courier_desc": "DHL GlobalMail Packet Priority",
                        "courier_name": "dhlec",
                        "transit_time": null,
                        "estimated_delivery_date": null
                    }
                }
            },
            "totals": {
                "subtotal": 143.34,
                "total": 158.12,
                "taxes": {
                    "gst": 2.75,
                    "qst": 5.470000000000001,
                    "hst": 6.56
                }
            },
            "documents": {
                "labels": {
                    "regular": "https://www.flagshipcompany.com/ship/20/order-docs/bbb02a8e17ada34a991c329da372f0a77debae9a",
                    "thermal": "https://www.flagshipcompany.com/ship/20/order-docs/bbb02a8e17ada34a991c329da372f0a77debae9a?document=therm"
                },
                "manifest": "https://www.flagshipcompany.com/ship/20/order-docs/bbb02a8e17ada34a991c329da372f0a77debae9a?document=manifest",
                "to_depot": {
                    "regular": "https://www.flagshipcompany.com/ship/3372195/labels/e5a146e501b543fd6de1c7289ce511f78c6d960c",
                    "thermal": "https://www.flagshipcompany.com/ship/3372195/labels/e5a146e501b543fd6de1c7289ce511f78c6d960c?document=therm"
                }
            }
        }';


        $this->getManifestByIdRequest = $this->getMockBuilder(GetManifestByIdRequest::class)
            ->setConstructorArgs(['testToken','localhost',20,'test','1.0.11'])
            ->setMethods(['execute'])
            ->getMock();
        $this->manifestRequest = $this->getManifestByIdRequest->execute();
        $this->manifest = new Manifest(json_decode($response));
    }
}
