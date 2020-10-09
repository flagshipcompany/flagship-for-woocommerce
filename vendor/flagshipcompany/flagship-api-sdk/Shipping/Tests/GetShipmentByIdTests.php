<?php
namespace Flagship\Shipping\Tests;

use \PHPUnit\Framework\TestCase;
use Flagship\Shipping\Objects\Shipment;
use Flagship\Shipping\Requests\GetShipmentByIdRequest;
use Flagship\Shipping\Objects\Package;

class GetShipmentByIdTests extends TestCase{

    public function testGetId(){
        $this->assertNotNull($this->shipment->getId());
        $this->assertSame(3372194,$this->shipment->getId());
    }

    public function testGetTrackingNumber(){
        $this->assertNotNull($this->shipment->getTrackingNumber());
        $this->assertSame("FS337219417D2B",$this->shipment->getTrackingNumber());
    }

    public function testGetStatus(){
        $this->assertNotNull($this->shipment->getStatus());
        $this->assertSame("dispatched",$this->shipment->getStatus());
    }

    public function testGetPickupId(){
        $this->assertNull($this->shipment->getPickupId());
    }

    public function testGetSenderCompany(){
        $this->assertNotNull($this->shipment->getSenderCompany());
        $this->assertSame('Flagship Courier Solutions',$this->shipment->getSenderCompany());
    }

    public function testGetSenderName(){
        $this->assertNotNull($this->shipment->getSenderName());
        $this->assertSame('Reception',$this->shipment->getSenderName());
    }

    public function testGetSenderAddress(){
        $this->assertNotNull($this->shipment->getSenderAddress());
        $this->assertSame('148 Brunswick', $this->shipment->getSenderAddress());
    }

    public function testGetSenderSuite(){
        $this->assertNull($this->shipment->getSenderSuite());
    }

    public function testGetSenderDepartment(){
        $this->assertSame(" ",$this->shipment->getSenderDepartment());
    }

    public function testGetSenderCity(){
        $this->assertNotNull($this->shipment->getSenderCity());
        $this->assertSame('Pointe-Claire',$this->shipment->getSenderCity());
    }

    public function testGetSenderCountry(){
        $this->assertNotNull($this->shipment->getSenderCountry());
        $this->assertSame("CA",$this->shipment->getSenderCountry());
    }

    public function testGetSenderState(){
        $this->assertNotNull($this->shipment->getSenderState());
        $this->assertSame('QC',$this->shipment->getSenderState());
    }

    public function testGetSenderPostalCode(){
        $this->assertNotNull($this->shipment->getSenderPostalCode());
        $this->assertSame("H9R 5P9",$this->shipment->getSenderPostalCode());
    }

    public function testGetSenderPhone(){
        $this->assertNotNull($this->shipment->getSenderPhone());
        $this->assertSame("5147390202",$this->shipment->getSenderPhone());
    }

    public function testGetSenderPhoneExt(){
        $this->assertNull($this->shipment->getSenderPhoneExt());
    }

    public function testGetSenderDetails(){
        $this->assertNotNull($this->shipment->getSenderDetails());
        $this->assertInternalType('array',$this->shipment->getSenderDetails());
    }

    public function testGetReceiverName(){
        $this->assertnotNull($this->shipment->getReceiverName());
        $this->assertSame("FlagShip Reception",$this->shipment->getReceiverName());
    }

    public function testGetRecieverCompany(){
        $this->assertNotNull($this->shipment->getReceiverCompany());
        $this->assertSame("FCS",$this->shipment->getReceiverCompany());
    }

    public function testGetReceiverAddress(){
        $this->assertNotNull($this->shipment->getReceiverAddress());
        $this->assertSame("Rue Avro",$this->shipment->getReceiverAddress());
    }

    public function testGetReceiverSuite(){
        $this->assertNotNull($this->shipment->getReceiverSuite());
        $this->assertSame("2251",$this->shipment->getReceiverSuite());
    }

    public function testGetReceiverDepartment(){
        $this->assertNotNull($this->shipment->getReceiverDepartment());
        $this->assertSame(" ",$this->shipment->getReceiverDepartment());
    }

    public function testIsReceiverCommercial(){
        $this->assertNotNull($this->shipment->IsReceiverCommercial());
        $this->assertTrue($this->shipment->IsReceiverCommercial());
    }

    public function testGetReceiverCity(){
        $this->assertNotNull($this->shipment->getReceiverCity());
        $this->assertSame("Montreal",$this->shipment->getReceiverCity());
    }

    public function testGetReceiverCountry(){
        $this->assertNotNull($this->shipment->getReceiverCountry());
        $this->assertSame("CA",$this->shipment->getReceiverCountry());
    }

    public function testGetReceiverState(){
        $this->assertNotNull($this->shipment->getReceiverState());
        $this->assertSame("QC",$this->shipment->getReceiverState());
    }

    public function testGetReceiverPostalCode(){
        $this->assertNotNull($this->shipment->getReceiverPostalCode());
        $this->assertSame("H1B 1N7",$this->shipment->getReceiverPostalCode());
    }

    public function testGetReceiverPhone(){
        $this->assertNotNull($this->shipment->getReceiverPhone());
        $this->assertSame("5144160304",$this->shipment->getReceiverPhone());
    }

    public function testGetReceiverPhoneExt(){
        $this->assertNull($this->shipment->getReceiverPhoneExt());
    }

    public function testGetReceiverDetails(){
        $this->assertNotNull($this->shipment->getReceiverDetails());
        $this->assertInternalType('array',$this->shipment->getReceiverDetails());
    }

    public function testGetReference(){
        $this->assertNull($this->shipment->getReference());
    }

    public function testGetDriverInstructions(){
        $this->assertNull($this->shipment->getDriverInstructions());
    }

    public function testIsSignatureRequired(){
        $this->assertNull($this->shipment->isSignatureRequired());
    }

    public function testGetShippingDate(){
        $this->assertNotNull($this->shipment->getShippingDate());
        $this->assertSame("2019-12-06",$this->shipment->getShippingDate());
    }

    public function testGetTrackingEmails(){
        $this->assertNull($this->shipment->getTrackingEmails());
    }

    public function testGetInsuranceValue(){
        $this->assertNotNull($this->shipment->getInsuranceValue());
        $this->assertSame(2000.00,$this->shipment->getInsuranceValue());
    }

    public function testGetInsuranceDescription(){
        $this->assertNotNull($this->shipment->getInsuranceDescription());
        $this->assertSame("Battle-ready lightsaber",$this->shipment->getInsuranceDescription());
    }

    public function testGetCodMethod(){
        $this->assertNull($this->shipment->getCodMethod());
    }

    public function testIsSaturdayDelivery(){
        $this->assertNull($this->shipment->IsSaturdayDelivery());
    }

    public function testGetCourierCode(){
        $this->assertNotNull($this->shipment->getCourierCode());
        $this->assertSame("PKY",$this->shipment->getCourierCode());
    }

    public function testGetCourierDescription(){
        $this->assertNotNull($this->shipment->getCourierDescription());
        $this->assertSame("DHL GlobalMail Packet Priority",$this->shipment->getCourierDescription());
    }

    public function testGetCourierName(){
        $this->assertNotNull($this->shipment->getCourierName());
        $this->assertSame("dhlec",$this->shipment->getCourierName());
    }

    public function testGetEstimatedDeliveryDate(){
        $this->assertNull($this->shipment->getEstimatedDeliveryDate());
    }

    public function testGetPackages(){
        $this->assertNotNull($this->shipment->getPackages());
        $this->assertInstanceOf(Package::class,$this->shipment->getPackages());
    }

    public function testGetPackageContent(){
        $this->assertNotNull($this->shipment->getPackageContent());
        $this->assertSame("goods",$this->shipment->getPackageContent());
    }

    public function testGetPackageUnits(){
        $this->assertNotNull($this->shipment->getPackageUnits());
        $this->assertSame("metric",$this->shipment->getPackageUnits());
    }

    public function testGetPackageType(){
        $this->assertNotNull($this->shipment->getPackageType());
        $this->assertSame("package",$this->shipment->getPackageType());
    }

    public function testGetItemsDetails(){
        $this->assertNotNull($this->shipment->getItemsDetails());
        $this->assertInternalType('array',$this->shipment->getItemsDetails());
    }

    public function testGetSubtotal(){
        $this->assertNotNull($this->shipment->getSubtotal());
        $this->assertSame(27.88,$this->shipment->getSubtotal());
    }

    public function testGetTotal(){
        $this->assertNotNull($this->shipment->getTotal());
        $this->assertSame(31.62,$this->shipment->getTotal());
    }

    public function testGetTaxesDetails(){
        $this->assertNotNull($this->shipment->getTaxesDetails());
        $this->assertSame([
            "gst"=> 1.25,
            "qst"=> 2.49
        ],$this->shipment->getTaxesDetails());
    }

    public function testGetTaxesTotal(){
        $this->assertNotNull($this->shipment->getTaxesTotal());
        $this->assertSame(3.74,$this->shipment->getTaxesTotal());
    }

    public function testGetCharges(){
        $this->assertNotNull($this->shipment->getCharges());
        $this->assertSame([
            "freight" => 2.88,
            "insurance" => 25
        ], $this->shipment->getCharges());
    }

    public function testGetAdjustments(){
        $this->assertNull($this->shipment->getAdjustments());
    }

    public function testGetLabel(){
        $this->assertNull($this->shipment->getLabel());
    }

    public function testGetThermalLabel(){
        $this->assertNull($this->shipment->getThermalLabel());
    }

    public function testGetCommercialInvoice(){
        $this->assertNull($this->shipment->getCommercialInvoice());
    }

    public function testGetTransitDetails(){
        $this->assertNotNull($this->shipment->getTransitDetails());
        $this->assertInternalType('array',$this->shipment->getTransitDetails());
    }

    public function testIsDocumentsOnly(){
        $this->assertNull($this->shipment->isDocumentsOnly());
    }

    public function testGetFlagshipCode(){
        $this->assertNull($this->shipment->getFlagshipCode());
    }

    public function testGetTransitTime(){
        $this->assertNull($this->shipment->getTransitTime());
    }


    protected function setUp(){

        $response = '{
            "id": 3372194,
            "tracking_number": "FS337219417D2B",
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
                "name": "FCS",
                "attn": "FlagShip Reception",
                "address": "Rue Avro",
                "suite": "2251",
                "department": " ",
                "is_commercial": true,
                "city": "Montreal",
                "country": "CA",
                "state": "QC",
                "postal_code": "H1B 1N7",
                "phone": "5144160304",
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
                        "pin": "FS337219417D2B",
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
            "invoice_details": [],
            "brokerage_details": null,
            "transit_details": [
                {
                    "status": "M",
                    "last_update": null,
                    "message": null,
                    "local_tracking_number": null
                }
            ]
        }';

        $this->getShipmentByIdRequest = $this->getMockBuilder(GetShipmentByIdRequest::class)
            ->setConstructorArgs(['testToken','localhost','test','1.0.11',3351627])
            ->setMethods(['execute'])
            ->getMock();
        $this->getShipment = $this->getShipmentByIdRequest->execute();
        $this->shipment = new Shipment(json_decode($response));
    }   
}