<?php

namespace Flagship\Shipping\Tests;

use Flagship\Shipping\Requests\AssociateShipmentRequest;
use \PHPUnit\Framework\TestCase;

class AssociateShipmentTests extends TestCase{

    public function testResponseCode(){
        $this->assertNull($this->associateShipmentRequest->getResponseCode());
    }

    public function testExecute(){
        $this->assertNotNull($this->associateShipmentRequest->execute());
        $this->assertSame(FALSE,$this->associateShipmentRequest->execute());
    }

    protected function setUp(){
        $this->associateShipmentRequest = $this->getMockBuilder(AssociateShipmentRequest::class)
            ->setConstructorArgs(['testToken','localhost',20,[],'test','1.0.11'])
            ->setMethods(['execute'])
            ->getMock();
    }
}

