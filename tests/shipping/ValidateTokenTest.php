<?php
use FlagshipWoocommerce\Requests\Validate_Token_Request;

class ValidateTokenTest extends FlagshipShippingUnitTestCase{

    public function setUp(){
        parent::setUp();
    }

    public function testValidateToken(){
        $request = new Validate_Token_Request('cQcoa5tK7F9HBmbx8cqlXBMFxEP3Tfb---mzKlBIM3Q',1);
        $return = $request->validateToken();
        $this->assertEquals(200, $return);
    }
}
