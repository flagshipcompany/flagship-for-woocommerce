<?php
namespace Flagship\Shipping\Tests;

use \PHPUnit\Framework\TestCase;
use Flagship\Shipping\Objects\Shipment;

class ShipmentTests extends TestCase{

    public function testGetId(){
       $this->assertNotNull($this->shipment->getId());
       $this->assertSame(2950191, $this->shipment->getId());
    }

    public function testGetTrackingNumber(){
       $this->assertNotNull($this->shipment->getTrackingNumber());
       $this->assertSame("1Z0075Y02099202915", $this->shipment->getTrackingNumber());
    }

    public function testGetStatus(){
       $this->assertNotNull($this->shipment->getStatus());
       $this->assertSame("dispatched", $this->shipment->getStatus());
    }

    public function testGetReference(){
       $this->assertNotNull($this->shipment->getReference());
       $this->assertSame("GetShipment reference here", $this->shipment->getReference());
    }

    public function testGetDriverInstructions(){
       $this->assertNotNull($this->shipment->getDriverInstructions());
       $this->assertSame("test", $this->shipment->getDriverInstructions());
    }

    public function testGetShippingDate(){
       $this->assertNotNull($this->shipment->getShippingDate());
       $this->assertSame("2018-10-29", $this->shipment->getShippingDate());
    }

    public function testGetCourierDescription(){
       $this->assertNotNull($this->shipment->getCourierDescription());
       $this->assertSame("UPS Standard", $this->shipment->getCourierDescription());
    }

    public function testGetEstimatedDeliveryDate(){
       $this->assertNotNull($this->shipment->getEstimatedDeliveryDate());
       $this->assertSame("2018-10-30 23:30:00", $this->shipment->getEstimatedDeliveryDate());
    }

    public function testGetTotal(){
       $this->assertNotNull($this->shipment->getTotal());
       $this->assertSame(49.66, $this->shipment->getTotal());
    }

    public function testGetSubTotal(){
       $this->assertNotNull($this->shipment->getSubTotal());
       $this->assertSame(43.19, $this->shipment->getSubTotal());
    }

    public function testGetTaxesTotal(){
       $this->assertNotNull($this->shipment->getTaxesTotal());
       $this->assertSame(6.47, $this->shipment->getTaxesTotal());
    }

    public function testGetLabel(){
       $this->assertNotNull($this->shipment->getLabel());
       $this->assertSame("https://flagshipcompany.com/ship/2950191/labels/b673d46530c04b0920f9b3d3f800c6c247be5232?document=reg", $this->shipment->getLabel());
    }

    public function testGetThermalLabel(){
       $this->assertNotNull($this->shipment->getThermalLabel());
       $this->assertSame("https://flagshipcompany.com/ship/2950191/labels/b673d46530c04b0920f9b3d3f800c6c247be5232?document=therm", $this->shipment->getThermalLabel());
    }

    public function testGetCommercialInvoice(){

       $this->assertSame(NULL, $this->shipment->getCommercialInvoice());
    }

    public function testGetSenderCompany(){
        $this->assertNotNull($this->shipment->getSenderCompany());
        $this->assertSame("FlagShip Courier Solutions", $this->shipment->getSenderCompany());
    }

    public function testGetSenderName(){
        $this->assertNotNull($this->shipment->getSenderName());
        $this->assertSame("Customer Service", $this->shipment->getSenderName());
    }

    public function testGetSenderAddress(){
        $this->assertNotNull($this->shipment->getSenderAddress());
        $this->assertSame("148 Brunswick", $this->shipment->getSenderAddress());
    }

    public function testGetSenderSuite(){
        $this->assertSame(NULL, $this->shipment->getSenderSuite());
    }

    public function testGetSenderDepartment(){
        $this->assertNotNull($this->shipment->getSenderDepartment());
        $this->assertSame(" ", $this->shipment->getSenderDepartment());
    }

    public function testGetSenderCity(){
        $this->assertNotNull($this->shipment->getSenderCity());
        $this->assertSame("Pointe-Claire", $this->shipment->getSenderCity());
    }

    public function testGetSenderCountry(){
        $this->assertNotNull($this->shipment->getSenderCountry());
        $this->assertSame("CA", $this->shipment->getSenderCountry());
    }

    public function testGetSenderState(){
        $this->assertNotNull($this->shipment->getSenderState());
        $this->assertSame("QC", $this->shipment->getSenderState());
    }

    public function testGetSenderPostalCode(){
        $this->assertNotNull($this->shipment->getSenderPostalCode());
        $this->assertSame("H9R 5P9", $this->shipment->getSenderPostalCode());
    }

    public function testGetSenderPhone(){
        $this->assertNotNull($this->shipment->getSenderPhone());
        $this->assertSame("18663208383", $this->shipment->getSenderPhone());
    }

    public function testGetSenderPhoneExt(){
        $this->assertSame(NULL, $this->shipment->getSenderPhoneExt());
    }

    public function testGetReceiverCompany(){
       $this->assertNotNull($this->shipment->getReceiverCompany());
       $this->assertSame("4 Designs", $this->shipment->getReceiverCompany());
   }

   public function testGetReceiverName(){
       $this->assertNotNull($this->shipment->getReceiverName());
       $this->assertSame("Helene Sachdeva", $this->shipment->getReceiverName());
   }

   public function testGetReceiverAddress(){
       $this->assertNotNull($this->shipment->getReceiverAddress());
       $this->assertSame("4985 Hickmore", $this->shipment->getReceiverAddress());
   }

   public function testGetReceiverSuite(){
       $this->assertSame(NULL, $this->shipment->getReceiverSuite());
   }

   public function testGetReceiverDepartment(){
       $this->assertNotNull($this->shipment->getReceiverDepartment());
       $this->assertSame(" ", $this->shipment->getReceiverDepartment());
   }

   public function testGetReceiverCity(){
       $this->assertNotNull($this->shipment->getReceiverCity());
       $this->assertSame("SAINT-LAURENT", $this->shipment->getReceiverCity());
   }

   public function testGetReceiverCountry(){
       $this->assertNotNull($this->shipment->getReceiverCountry());
       $this->assertSame("CA", $this->shipment->getReceiverCountry());
   }

   public function testGetReceiverState(){
       $this->assertNotNull($this->shipment->getReceiverState());
       $this->assertSame("QC", $this->shipment->getReceiverState());
   }

   public function testGetReceiverPostalCode(){
       $this->assertNotNull($this->shipment->getReceiverPostalCode());
       $this->assertSame("H4T 1J5", $this->shipment->getReceiverPostalCode());
   }

   public function testGetReceiverPhone(){
       $this->assertNotNull($this->shipment->getReceiverPhone());
       $this->assertSame("5147724712", $this->shipment->getReceiverPhone());
   }

   public function testGetReceiverPhoneExt(){
       $this->assertSame(NULL, $this->shipment->getReceiverPhoneExt());
   }

    protected function setup(){
        $response  = '            {
                "id": 2950191,
                "tracking_number": "1Z0075Y02099202915",
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
                    "reference": "GetShipment reference here",
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
            }';
        $this->shipment = $this->getMockBuilder(Shipment::class)
                          ->setConstructorArgs([json_decode($response)])
                          ->setMethods(['__construct'])
                          ->getMock();
    }
}
