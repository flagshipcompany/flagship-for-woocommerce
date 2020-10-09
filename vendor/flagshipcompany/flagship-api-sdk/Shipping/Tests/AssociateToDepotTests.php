<?php
namespace Flagship\Shipping\Tests;

use Flagship\Shipping\Requests\AssociateToDepotRequest;
use \PHPUnit\Framework\TestCase;

class AssociateToDepotTests extends TestCase{

    public function testGetResponseCode(){
        $this->assertNull($this->associateToDepotRequest->getResponseCode());
    }

    public function testExecute(){
        $this->assertNotNull($this->associateToDepotRequest->execute());
        $this->assertSame(FALSE,$this->associateToDepotRequest->execute());
    }
    

    protected function setUp(){
        $this->associateToDepotRequest = $this->getMockBuilder(AssociateToDepotRequest::class)
            ->setConstructorArgs(['testToken','localhost',20,[],'test','1.0.11'])
            ->setMethods(['execute'])
            ->getMock();
    }
}
