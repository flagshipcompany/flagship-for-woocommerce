<?php

namespace Flagship\Shipping\Tests;

use \PHPUnit\Framework\TestCase;
use Flagship\Shipping\Requests\CancelManifestByIdRequest;

class CancelManifestByIdTests extends TestCase{
    
    public function testResponseCode(){
        $this->assertNull($this->cancelManifestByIdRequest->getResponseCode());
    }

    public function testExecute(){
        $this->assertNotNull($this->cancelManifestByIdRequest->execute());
        $this->assertSame(FALSE,$this->cancelManifestByIdRequest->execute());
    }

    protected function setUp(){
        $this->cancelManifestByIdRequest = $this->getMockBuilder(CancelManifestByIdRequest::class)
                    ->setConstructorArgs(['testToken','localhost',84,'test','1.0.11'])
                    ->setMethods(['execute'])
                    ->getMock();
    }
}
