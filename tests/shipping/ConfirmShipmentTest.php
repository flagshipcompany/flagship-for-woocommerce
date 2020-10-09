<?php

use FlagshipWoocommerce\Requests\Confirm_Shipment_Request;

class ConfirmShipmentTest extends FlagshipShippingUnitTestCase{

    public function setUp(){
        parent::setUp();
    }

    public function testConfirmShipment(){
        $request = new Confirm_Shipment_Request('cQcoa5tK7F9HBmbx8cqlXBMFxEP3Tfb---mzKlBIM3Q','https://test-api.smartship.io');
        $shipmentId = 1432067;
        $shipment = $request->confirmShipmentById($shipmentId);
        if(is_string($shipment)){
            $errorArray = json_decode($shipment,true);
            $this->assertArrayHasKey('error',$errorArray);
            return;
        }
        $this->assertEquals('dispatched', $shipment->getStatus());
    }
}
