<?php

use \PHPUnit\Framework\TestCase;
use Flagship\Shipping\Requests\ValidateTokenRequest;

class ValidateTokenTests extends TestCase{

    public function testExecute(){
        $this->assertNotNull($this->validateTokenRequest->execute());
        $this->assertSame(FALSE,$this->validateTokenRequest->execute());
    }

    public function testGetResponseCode(){
        $this->assertNull($this->validateTokenRequest->getResponseCode());
    }

    protected function setUp(){
        $this->validateTokenRequest = $this->getMockBuilder(ValidateTokenRequest::class)
                                        ->setConstructorArgs(['localhost','testToken','testing','1.0.11'])
                                        ->setMethods(['execute'])
                                        ->getMock();
    }
}