<?php

namespace Flagship\Shipping\Tests;
use \PHPUnit\Framework\TestCase;
use Flagship\Shipping\Objects\TrackShipment;

class TrackShipmentTests extends TestCase{

    public function testGetCurrentStatus(){
        $this->assertNotNull($this->trackShipment->getCurrentStatus());
        $this->assertSame('T',$this->trackShipment->getCurrentStatus());
    }

    public function testGetShipmentId(){
        $this->assertNotNull($this->trackShipment->getShipmentId());
        $this->assertSame(3243056,$this->trackShipment->getShipmentId());
    }

    public function testGetStatusDescription(){
        $this->assertNotNull($this->trackShipment->getStatusDescription());
        $this->assertSame("DEPARTURE SCAN - Concord, ON, CA",$this->trackShipment->getStatusDescription());
    }

    public function testGetCourierUpdate(){
        $this->assertSame("2019-04-03 01:20:00",$this->trackShipment->getCourierUpdate());
    }

    protected function setup(){
        $response = '{
            "shipment_id": "3243056",
            "current_status": "T",
            "status_desc": "DEPARTURE SCAN - Concord, ON, CA",
            "courier_update": "2019-04-03 01:20:00"
        }';
        $this->trackShipment = $this->getMockBuilder(TrackShipment::class)
                          ->setConstructorArgs([json_decode($response)])
                          ->setMethods(['__construct'])
                          ->getMock();
    }
}
