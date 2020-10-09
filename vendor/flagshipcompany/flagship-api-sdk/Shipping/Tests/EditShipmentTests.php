<?php
namespace Flagship\Shipping\Tests;

use \PHPUnit\Framework\TestCase;
use Flagship\Shipping\Objects\Shipment;

class EditShipmentTests extends TestCase{
 
    public function testGetId(){
        $this->assertNotEmpty($this->editShipment->getId());
        $this->assertNotNull($this->editShipment->getId());
        $this->assertSame(2950284,$this->editShipment->getId());
    }

    public function testGetTrackingNumber(){
        $this->assertSame(NULl,$this->editShipment->getTrackingNumber());
    }

    public function testGetStatus(){
        $this->assertNotEmpty($this->editShipment->getStatus());
        $this->assertNotNull($this->editShipment->getStatus());
        $this->assertSame('prequoted',$this->editShipment->getStatus());
    }

    public function testGetPickupId(){
        $this->assertSame(NULl,$this->editShipment->getPickupId());
    }

    public function testGetSenderCompany(){
        $this->assertNotEmpty($this->editShipment->getSenderCompany());
        $this->assertNotNull($this->editShipment->getSenderCompany());
        $this->assertSame('WooComm',$this->editShipment->getSenderCompany());
    }

    public function testGetSenderName(){
        $this->assertNotEmpty($this->editShipment->getSenderName());
        $this->assertNotNull($this->editShipment->getSenderName());
        $this->assertSame('WooComm',$this->editShipment->getSenderName());
    }

    public function testGetSenderAddress(){
        $this->assertNotEmpty($this->editShipment->getSenderAddress());
        $this->assertNotNull($this->editShipment->getSenderAddress());
        $this->assertSame('148 Boul. Brunswick',$this->editShipment->getSenderAddress());
    }

    public function testGetSenderSuite(){
        $this->assertSame(NULL,$this->editShipment->getSenderSuite());
    }

    public function testGetSenderDepartment(){
        $this->assertSame(" ",$this->editShipment->getSenderDepartment());
    }

    public function testGetSenderCity(){
        $this->assertNotEmpty($this->editShipment->getSenderCity());
        $this->assertNotNull($this->editShipment->getSenderCity());
        $this->assertSame('Pointe',$this->editShipment->getSenderCity());
    }

    public function testGetSenderCountry(){
        $this->assertNotEmpty($this->editShipment->getSenderCountry());
        $this->assertNotNull($this->editShipment->getSenderCountry());
        $this->assertSame('CA',$this->editShipment->getSenderCountry());
    }

    public function testGetSenderState(){
        $this->assertNotEmpty($this->editShipment->getSenderState());
        $this->assertNotNull($this->editShipment->getSenderState());
        $this->assertSame('QC',$this->editShipment->getSenderState());
    }

    public function testGetSenderPostalCode(){
        $this->assertNotEmpty($this->editShipment->getSenderPostalCode());
        $this->assertNotNull($this->editShipment->getSenderPostalCode());
        $this->assertSame('H9R 5P9',$this->editShipment->getSenderPostalCode());
    }

    public function testGetSenderPhone(){
        $this->assertNotEmpty($this->editShipment->getSenderPhone());
        $this->assertNotNull($this->editShipment->getSenderPhone());
        $this->assertSame('5148936016',$this->editShipment->getSenderPhone());
    }

    public function testGetSenderPhoneExt(){
        $this->assertSame(NULL,$this->editShipment->getSenderPhoneExt());
    }

    public function testGetSenderDetails(){
        $expected = [
            "name"=> "WooComm",
            "attn"=> "WooComm",
            "address"=> "148 Boul. Brunswick",
            "suite"=> null,
            "department"=> " ",
            "city"=> "Pointe",
            "country"=> "CA",
            "state"=> "QC",
            "postal_code"=> "H9R 5P9",
            "phone"=> "5148936016",
            "phone_ext"=> null
        ];

        $this->assertNotEmpty($this->editShipment->getSenderDetails());
        $this->assertNotNull($this->editShipment->getSenderDetails());
        $this->assertSame($expected, $this->editShipment->getSenderDetails());
    }

    public function testGetReceiverCompany(){
        $this->assertNotEmpty($this->editShipment->getReceiverCompany());
        $this->assertNotNull($this->editShipment->getReceiverCompany());
        $this->assertSame('Smith Associates',$this->editShipment->getReceiverCompany());
    }


    public function testGetReceiverName(){
        $this->assertNotEmpty($this->editShipment->getReceiverName());
        $this->assertNotNull($this->editShipment->getReceiverName());
        $this->assertSame('Adam Smith',$this->editShipment->getReceiverName());
    }


    public function testGetReceiverAddress(){
        $this->assertNotEmpty($this->editShipment->getReceiverAddress());
        $this->assertNotNull($this->editShipment->getReceiverAddress());
        $this->assertSame('691 Williams Avenue',$this->editShipment->getReceiverAddress());
    }

    public function testGetReceiverSuite(){
        $this->assertNotEmpty($this->editShipment->getReceiverSuite());
        $this->assertNotNull($this->editShipment->getReceiverSuite());
        $this->assertSame('54',$this->editShipment->getReceiverSuite());
    }

    public function testGetReceiverDepartment(){
        $this->assertSame(" ",$this->editShipment->getReceiverDepartment());
    }


    public function testIsReceiverCommercial(){
        $this->assertNotNull($this->editShipment->IsReceiverCommercial());
        $this->assertSame(FALSE,$this->editShipment->IsReceiverCommercial());
    }

    public function testGetReceiverCity(){
        $this->assertNotEmpty($this->editShipment->getReceiverCity());
        $this->assertNotNull($this->editShipment->getReceiverCity());
        $this->assertSame('dorval',$this->editShipment->getReceiverCity());
    }

    public function testGetReceiverCountry(){
        $this->assertNotEmpty($this->editShipment->getReceiverCountry());
        $this->assertNotNull($this->editShipment->getReceiverCountry());
        $this->assertSame('CA',$this->editShipment->getReceiverCountry());
    }

    public function testGetReceiverState(){
        $this->assertNotEmpty($this->editShipment->getReceiverState());
        $this->assertNotNull($this->editShipment->getReceiverState());
        $this->assertSame('QC',$this->editShipment->getReceiverState());
    }

    public function testGetReceiverPostalCode(){
        $this->assertNotEmpty($this->editShipment->getReceiverPostalCode());
        $this->assertNotNull($this->editShipment->getReceiverPostalCode());
        $this->assertSame('Y9S 5K6',$this->editShipment->getReceiverPostalCode());
    }

    public function testGetReceiverPhone(){
        $this->assertNotEmpty($this->editShipment->getReceiverPhone());
        $this->assertNotNull($this->editShipment->getReceiverPhone());
        $this->assertSame('5148915618',$this->editShipment->getReceiverPhone());
    }

    public function testGetReceiverPhoneExt(){
        $this->assertNotEmpty($this->editShipment->getReceiverPhoneExt());
        $this->assertNotNull($this->editShipment->getReceiverPhoneExt());
        $this->assertSame('248',$this->editShipment->getReceiverPhoneExt());
    }

    public function testGetReceiverDetails(){
        $expected = [
            "name"=> "Smith Associates",
            "attn"=> "Adam Smith",
            "address"=> "691 Williams Avenue",
            "suite"=> "54",
            "department"=> " ",
            "is_commercial"=> false,
            "city"=> "dorval",
            "country"=> "CA",
            "state"=> "QC",
            "postal_code"=> "Y9S 5K6",
            "phone"=> "5148915618",
            "phone_ext"=> "248"
        ];

        $this->assertNotEmpty($this->editShipment->getReceiverDetails());
        $this->assertNotNull($this->editShipment->getReceiverDetails());
        $this->assertSame($expected, $this->editShipment->getReceiverDetails());
    }

    public function testGetReference(){
        $this->assertNotEmpty($this->editShipment->getReference());
        $this->assertNotNull($this->editShipment->getReference());
        $this->assertSame('Order for Adam Smith',$this->editShipment->getReference());
    }

    public function testGetDriverInstructions(){
        $this->assertSame(NULL,$this->editShipment->getDriverInstructions());
    }

    public function testGetShippingDate(){
        $this->assertNotEmpty($this->editShipment->getShippingDate());
        $this->assertNotNull($this->editShipment->getShippingDate());
        $this->assertSame('2018-11-01',$this->editShipment->getShippingDate());
    }

    public function testGetTrackingEmails(){
        $this->assertSame(NULL,$this->editShipment->getTrackingEmails());
    }

    public function testGetInsuranceValue(){
        $this->assertSame(NULL,$this->editShipment->getInsuranceValue());
    }

    public function testGetInsuranceDescription(){
        $this->assertSame(NULL,$this->editShipment->getInsuranceDescription());
    }

    public function testGetCodMethod(){
        $this->assertSame(NULL,$this->editShipment->getCodMethod());
    }


    public function testGetCodPayableTo(){
        $this->assertSame(NULL,$this->editShipment->getCodPayableTo());
    }


    public function testGetCodReceiverPhone(){
        $this->assertSame(NULL,$this->editShipment->getCodReceiverPhone());
    }

    public function testGetCodAmount(){
        $this->assertSame(NULL,$this->editShipment->getCodAmount());
    }

    public function testGetCodCurrency(){
        $this->assertSame(NULL,$this->editShipment->getCodCurrency());
    }

    public function testIsSaturdayDelivery(){
        $this->assertSame(NULL,$this->editShipment->IsSaturdayDelivery());
    }

    public function testGetCourierCode(){
        $this->assertSame(NULL,$this->editShipment->getCourierCode());
    }

    public function testGetCourierDescription(){
        $this->assertSame(NULL,$this->editShipment->getCourierDescription());
    }

    public function testGetCourierName(){
        $this->assertSame('N/A',$this->editShipment->getCourierName());
    }

    public function testGetEstimatedDeliveryDate(){
        $this->assertSame(NULL,$this->editShipment->getEstimatedDeliveryDate());
    }

    public function testGetPackageContent(){
        $this->assertNotEmpty($this->editShipment->getPackageContent());
        $this->assertNotNull($this->editShipment->getPackageContent());
        $this->assertSame('goods',$this->editShipment->getPackageContent());
    }

    public function testGetPackageUnits(){
        $this->assertNotEmpty($this->editShipment->getPackageUnits());
        $this->assertNotNull($this->editShipment->getPackageUnits());
        $this->assertSame('metric',$this->editShipment->getPackageUnits());
    }

    public function testGetPackageType(){
        $this->assertNotEmpty($this->editShipment->getPackageType());
        $this->assertNotNull($this->editShipment->getPackageType());
        $this->assertSame('package',$this->editShipment->getPackageType());
    }

    public function testGetItemsDetails(){
        $expected = [

        json_decode('{
            "pin": null,
            "height": 1,
            "length": 1,
            "width": 1,
            "weight": 10,
            "description": null
        }')
  
        ];

        $this->assertNotEmpty($this->editShipment->getItemsDetails());
        $this->assertNotNull($this->editShipment->getItemsDetails());
        $this->assertEquals($expected, $this->editShipment->getItemsDetails());
    }

    public function testGetSubtotal(){
        $this->assertNotNull($this->editShipment->getSubtotal());
        $this->assertSame(0.00,$this->editShipment->getSubtotal());
    }

    public function testGetTotal(){
        $this->assertNotNull($this->editShipment->getTotal());
        $this->assertSame(0.00,$this->editShipment->getTotal());
    }

    public function testGetTaxesDetails(){
        $this->assertSame(NULL,$this->editShipment->getTaxesDetails());
    }

    public function testGetTaxesTotal(){
        $this->assertNotNull($this->editShipment->getTaxesTotal());
        $this->assertSame(0.00,$this->editShipment->getTaxesTotal());
    }

    protected function setUp() {
        $response = '{
        "id": 2950284,
        "tracking_number": null,
        "status": "prequoted",
        "pickup_id": null,
        "from": {
            "name": "WooComm",
            "attn": "WooComm",
            "address": "148 Boul. Brunswick",
            "suite": null,
            "department": " ",
            "city": "Pointe",
            "country": "CA",
            "state": "QC",
            "postal_code": "H9R 5P9",
            "phone": "5148936016",
            "phone_ext": null
        },
        "to": {
            "name": "Smith Associates",
            "attn": "Adam Smith",
            "address": "691 Williams Avenue",
            "suite": "54",
            "department": " ",
            "is_commercial": false,
            "city": "dorval",
            "country": "CA",
            "state": "QC",
            "postal_code": "Y9S 5K6",
            "phone": "5148915618",
            "phone_ext": "248"
        },
        "options": {
            "signature_required": "true",
            "reference": "Order for Adam Smith",
            "shipping_date": "2018-11-01"
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
                    "weight": 10,
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
        "invoice_details": [],
        "brokerage_details": null
    }';

    $this->editShipment = $this->getMockBuilder(Shipment::class)
                          ->setConstructorArgs([json_decode($response)])
                          ->setMethods(['__construct']) 
                          ->getMock();
    }
}