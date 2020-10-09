<?php

namespace Flagship\Shipping\Tests;

use \PHPUnit\Framework\TestCase;
use Flagship\Shipping\Objects\Shipment;

class PrepareShipmentTests extends TestCase{

    public function testGetId(){
        $this->assertNotEmpty($this->preparedShipment->getId());
        $this->assertNotNull($this->preparedShipment->getId());
        $this->assertSame(2950226,$this->preparedShipment->getId());
    }

    public function testGetTrackingNumber(){              
        $this->assertSame(NULL,$this->preparedShipment->getTrackingNumber());
    }

    public function testGetStatus(){
        $this->assertNotEmpty($this->preparedShipment->getStatus());
        $this->assertNotNull($this->preparedShipment->getStatus());
        $this->assertSame('prequoted',$this->preparedShipment->getStatus());
    }

    public function testGetPickupId(){
        $this->assertSame(NULL,$this->preparedShipment->getPickupId());
    }

    public function testGetSenderCompany(){
        $this->assertNotEmpty($this->preparedShipment->getSenderCompany());
        $this->assertNotNull($this->preparedShipment->getSenderCompany());
        $this->assertSame('ACME inc',$this->preparedShipment->getSenderCompany());
    }

    public function testGetSenderName(){
        $this->assertNotEmpty($this->preparedShipment->getSenderName());
        $this->assertNotNull($this->preparedShipment->getSenderName());
        $this->assertSame('Bob',$this->preparedShipment->getSenderName());
    }

    public function testGetSenderAddress(){
        $this->assertNotEmpty($this->preparedShipment->getSenderAddress());
        $this->assertNotNull($this->preparedShipment->getSenderAddress());
        $this->assertSame('123 Main Street',$this->preparedShipment->getSenderAddress());
    }

    public function testGetSenderSuite(){
        $this->assertNotEmpty($this->preparedShipment->getSenderSuite());
        $this->assertNotNull($this->preparedShipment->getSenderSuite());
        $this->assertSame('227',$this->preparedShipment->getSenderSuite());
    }

    public function testGetSenderDepartment(){
        $this->assertNotEmpty($this->preparedShipment->getSenderDepartment());
        $this->assertNotNull($this->preparedShipment->getSenderDepartment());
        $this->assertSame('Reception',$this->preparedShipment->getSenderDepartment());
    }

    public function testGetSenderCity(){
        $this->assertNotEmpty($this->preparedShipment->getSenderCity());
        $this->assertNotNull($this->preparedShipment->getSenderCity());
        $this->assertSame('Roxboro',$this->preparedShipment->getSenderCity());
    }

    public function testGetSenderCountry(){
        $this->assertNotEmpty($this->preparedShipment->getSenderCountry());
        $this->assertNotNull($this->preparedShipment->getSenderCountry());
        $this->assertSame('CA',$this->preparedShipment->getSenderCountry());
    }

    public function testGetSenderState(){
        $this->assertNotEmpty($this->preparedShipment->getSenderState());
        $this->assertNotNull($this->preparedShipment->getSenderState());
        $this->assertSame('QC',$this->preparedShipment->getSenderState());
    }

    public function testGetSenderPostalCode(){
        $this->assertNotEmpty($this->preparedShipment->getSenderPostalCode());
        $this->assertNotNull($this->preparedShipment->getSenderPostalCode());
        $this->assertSame('H8Y 2T3',$this->preparedShipment->getSenderPostalCode());
    }

    public function testGetSenderPhone(){
        $this->assertNotEmpty($this->preparedShipment->getSenderPhone());
        $this->assertNotNull($this->preparedShipment->getSenderPhone());
        $this->assertSame('18663208383',$this->preparedShipment->getSenderPhone());
    }

    public function testGetSenderPhoneExt(){
        $this->assertSame(NULL,$this->preparedShipment->getSenderPhoneExt());
    }

    public function testGetSenderDetails(){
        $expected = [
            "name"=> "ACME inc",
            "attn"=> "Bob",
            "address"=> "123 Main Street",
            "suite"=> "227",
            "department"=> "Reception",
            "city"=> "Roxboro",
            "country"=> "CA",
            "state"=> "QC",
            "postal_code"=> "H8Y 2T3",
            "phone"=> "18663208383",
            "phone_ext"=> null
        ];
        $this->assertNotNull($this->preparedShipment->getSenderDetails());
        $this->assertNotEmpty($this->preparedShipment->getSenderDetails());
        $this->assertSame($expected, $this->preparedShipment->getSenderDetails());
    }

    public function testGetReceiverCompany(){
        $this->assertNotEmpty($this->preparedShipment->getReceiverCompany());
        $this->assertNotNull($this->preparedShipment->getReceiverCompany());
        $this->assertSame('ACME inc',$this->preparedShipment->getReceiverCompany());
    }

    public function testGetReceiverName(){
        $this->assertNotEmpty($this->preparedShipment->getReceiverName());
        $this->assertNotNull($this->preparedShipment->getReceiverName());
        $this->assertSame('Bob',$this->preparedShipment->getReceiverName());
    }

    public function testGetReceiverAddress(){
        $this->assertNotEmpty($this->preparedShipment->getReceiverAddress());
        $this->assertNotNull($this->preparedShipment->getReceiverAddress());
        $this->assertSame('123 Main Street',$this->preparedShipment->getReceiverAddress());
    }

    public function testGetReceiverSuite(){
        $this->assertNotEmpty($this->preparedShipment->getReceiverSuite());
        $this->assertNotNull($this->preparedShipment->getReceiverSuite());
        $this->assertSame('148',$this->preparedShipment->getReceiverSuite());
    }

    public function testGetReceiverDepartment(){
        $this->assertNotEmpty($this->preparedShipment->getReceiverDepartment());
        $this->assertNotNull($this->preparedShipment->getReceiverDepartment());
        $this->assertSame('Reception',$this->preparedShipment->getReceiverDepartment());
    }

    public function testGetReceiverCity(){
        $this->assertNotEmpty($this->preparedShipment->getReceiverCity());
        $this->assertNotNull($this->preparedShipment->getReceiverCity());
        $this->assertSame('Pointe-Claire',$this->preparedShipment->getReceiverCity());
    }

    public function testGetReceiverCountry(){
        $this->assertNotEmpty($this->preparedShipment->getReceiverCountry());
        $this->assertNotNull($this->preparedShipment->getReceiverCountry());
        $this->assertSame('CA',$this->preparedShipment->getReceiverCountry());
    }

    public function testGetReceiverState(){
        $this->assertNotEmpty($this->preparedShipment->getReceiverState());
        $this->assertNotNull($this->preparedShipment->getReceiverState());
        $this->assertSame('QC',$this->preparedShipment->getReceiverState());
    }

    public function testGetReceiverPostalCode(){
        $this->assertNotEmpty($this->preparedShipment->getReceiverPostalCode());
        $this->assertNotNull($this->preparedShipment->getReceiverPostalCode());
        $this->assertSame('H9R 5P9',$this->preparedShipment->getReceiverPostalCode());
    }

    public function testGetReceiverPhone(){
        $this->assertNotEmpty($this->preparedShipment->getReceiverPhone());
        $this->assertNotNull($this->preparedShipment->getReceiverPhone());
        $this->assertSame('18663208383',$this->preparedShipment->getReceiverPhone());
    }

    public function testGetReceiverPhoneExt(){
        $this->assertSame(NULL,$this->preparedShipment->getReceiverPhoneExt());
    }

    public function testGetReceiverDetails(){
        $expected = [
            "name"=> "ACME inc",
            "attn"=> "Bob",
            "address"=> "123 Main Street",
            "suite"=> "148",
            "department"=> "Reception",
            "is_commercial"=> true,
            "city"=> "Pointe-Claire",
            "country"=> "CA",
            "state"=> "QC",
            "postal_code"=> "H9R 5P9",
            "phone"=> "18663208383",
            "phone_ext"=> null
        ];
        $this->assertNotEmpty($this->preparedShipment->getReceiverDetails());
        $this->assertNotNull($this->preparedShipment->getReceiverDetails());
        $this->assertSame($expected, $this->preparedShipment->getReceiverDetails());
    }

    public function testIsSaturdayDelivery(){
        $this->assertNotEmpty($this->preparedShipment->isSaturdayDelivery());
        $this->assertNotNull($this->preparedShipment->isSaturdayDelivery());
        $this->assertSame(TRUE,$this->preparedShipment->isSaturdayDelivery());
    }

    public function testGetInsuranceValue(){
        $this->assertNotEmpty($this->preparedShipment->getInsuranceValue());
        $this->assertNotNull($this->preparedShipment->getInsuranceValue());
        $this->assertSame(123.45,$this->preparedShipment->getInsuranceValue());
    }

    public function testGetInsuranceDescription(){
        $this->assertNotEmpty($this->preparedShipment->getInsuranceDescription());
        $this->assertNotNull($this->preparedShipment->getInsuranceDescription());
        $this->assertSame('Children books',$this->preparedShipment->getInsuranceDescription());
    }

    public function testGetCodMethod(){
        $this->assertNotEmpty($this->preparedShipment->getCodMethod());
        $this->assertNotNull($this->preparedShipment->getCodMethod());
        $this->assertSame('check',$this->preparedShipment->getCodMethod());
    }

    public function testGetCodPayableTo(){
        $this->assertNotEmpty($this->preparedShipment->getCodPayableTo());
        $this->assertNotNull($this->preparedShipment->getCodPayableTo());
        $this->assertSame('Bob',$this->preparedShipment->getCodPayableTo());
    }

    public function testGetCodReceiverPhone(){
        $this->assertNotEmpty($this->preparedShipment->getCodReceiverPhone());
        $this->assertNotNull($this->preparedShipment->getCodReceiverPhone());
        $this->assertSame('18663208383',$this->preparedShipment->getCodReceiverPhone());
    }

    public function testGetCodAmount(){
        $this->assertNotEmpty($this->preparedShipment->getCodAmount());
        $this->assertNotNull($this->preparedShipment->getCodAmount());
        $this->assertSame(123.45,$this->preparedShipment->getCodAmount());
    }

    public function testGetCodCurrency(){
        $this->assertNotEmpty($this->preparedShipment->getCodCurrency());
        $this->assertNotNull($this->preparedShipment->getCodCurrency());
        $this->assertSame('CAD',$this->preparedShipment->getCodCurrency());
    }

    public function testGetReference(){
        $this->assertNotEmpty($this->preparedShipment->getReference());
        $this->assertNotNull($this->preparedShipment->getReference());
        $this->assertSame('123 test',$this->preparedShipment->getReference());
    }

    public function testGetDriverInstructions(){
        $this->assertNotEmpty($this->preparedShipment->getDriverInstructions());
        $this->assertNotNull($this->preparedShipment->getDriverInstructions());
        $this->assertSame('Doorbell broken, knock on door',$this->preparedShipment->getDriverInstructions());
    }

    public function testGetShippingDate(){
        $this->assertNotEmpty($this->preparedShipment->getShippingDate());
        $this->assertNotNull($this->preparedShipment->getShippingDate());
        $this->assertSame('2018-12-11',$this->preparedShipment->getShippingDate());
    }

    public function testGetTrackingEmails(){
        $this->assertNotEmpty($this->preparedShipment->getTrackingEmails());
        $this->assertNotNull($this->preparedShipment->getTrackingEmails());
        $this->assertSame('jbeans@company.com;shipping1@company.com',$this->preparedShipment->getTrackingEmails());
    }

    public function testGetCourierCode(){
        $this->assertSame(NULL,$this->preparedShipment->getCourierCode());
    }

    public function testGetCourierDescription(){
        $this->assertSame(NULL,$this->preparedShipment->getCourierDescription());
    }

    public function testGetCourierName(){
        $this->assertNotEmpty($this->preparedShipment->getCourierName());
        $this->assertNotNull($this->preparedShipment->getCourierName());
        $this->assertSame('N/A',$this->preparedShipment->getCourierName());
    }

    public function testGetEstimatedDeliveryDate(){
        $this->assertSame(NULL,$this->preparedShipment->getEstimatedDeliveryDate());
    }

    public function testGetPackageContent(){
        $this->assertNotEmpty($this->preparedShipment->getPackageContent());
        $this->assertNotNull($this->preparedShipment->getPackageContent());
        $this->assertSame('goods',$this->preparedShipment->getPackageContent());
    }

    public function testGetPackageUnits(){
        $this->assertNotEmpty($this->preparedShipment->getPackageUnits());
        $this->assertNotNull($this->preparedShipment->getPackageUnits());
        $this->assertSame('imperial',$this->preparedShipment->getPackageUnits());
    }

    public function testGetPackageType(){
        $this->assertNotEmpty($this->preparedShipment->getPackageType());
        $this->assertNotNull($this->preparedShipment->getPackageType());
        $this->assertSame('package',$this->preparedShipment->getPackageType());
    }

    public function testGetItemsDetails(){
        $expected = [
            json_decode('{
                    "pin": null,
                    "height": "12.00",
                    "length": "12.00",
                    "width": "128778.00",
                    "weight": "90.00",
                    "description": "Item description"
                }')
            ];

        $this->assertNotEmpty($this->preparedShipment->getItemsDetails());
        $this->assertNotNull($this->preparedShipment->getItemsDetails());
        $this->assertEquals($expected, $this->preparedShipment->getItemsDetails());
    }

    protected function setUp() {
        $response = '{
            "id": 2950226,
            "tracking_number": null,
            "status": "prequoted",
            "pickup_id": null,
            "from": {
                "name": "ACME inc",
                "attn": "Bob",
                "address": "123 Main Street",
                "suite": "227",
                "department": "Reception",
                "city": "Roxboro",
                "country": "CA",
                "state": "QC",
                "postal_code": "H8Y 2T3",
                "phone": "18663208383",
                "phone_ext": null
            },
            "to": {
                "name": "ACME inc",
                "attn": "Bob",
                "address": "123 Main Street",
                "suite": "148",
                "department": "Reception",
                "is_commercial": true,
                "city": "Pointe-Claire",
                "country": "CA",
                "state": "QC",
                "postal_code": "H9R 5P9",
                "phone": "18663208383",
                "phone_ext": null
            },
            "options": {
                "saturday_delivery": "true",
                "insurance": {
                    "value": 123.45,
                    "description": "Children books"
                },
                "cod": {
                    "method": "check",
                    "payable_to": "Bob",
                    "receiver_phone": "18663208383",
                    "amount": 123.45,
                    "currency": "CAD"
                },
                "reference": "123 test",
                "driver_instructions": "Doorbell broken, knock on door",
                "shipping_date": "2018-12-11",
                "tracking_emails": "jbeans@company.com;shipping1@company.com"
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
                "units": "imperial",
                "type": "package",
                "items": [
                    {
                        "pin": null,
                        "height": "12.00",
                        "length": "12.00",
                        "width": "128778.00",
                        "weight": "90.00",
                        "description": "Item description"
                    }
                ]
            },
            "invoice_details": [],
            "brokerage_details": null
        }';

        $this->preparedShipment = $this->getMockBuilder(Shipment::class)
                                        ->setConstructorArgs([json_decode($response)])
                                        ->setMethods(['__construct']) 
                                        ->getMock();
    }
}