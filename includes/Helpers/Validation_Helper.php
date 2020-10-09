<?php
namespace FlagshipWoocommerce\Helpers;
use FlagshipWoocommerce\Requests\Validate_Token_Request;

class Validation_Helper {

    public function __construct($testEnv = 0)
    {
        $this->testEnv = $testEnv;
    }

    public static function validateMultiEmails($emails) {
        $emails = explode(';', trim($emails));

        $invalidEmails = array_filter($emails, function($val) {
            return is_email(trim($val)) === false;
        });

        return count($invalidEmails) == 0;
    }

    public function validateToken($token)
    {
        $validateTokenRequest = new Validate_Token_Request($token, $this->testEnv);
        $validateToken = $validateTokenRequest->validateToken() == 200 ? true : false;
        return $validateToken;
    }
}
