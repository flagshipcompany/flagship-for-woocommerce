<?php
namespace FlagshipWoocommerce\Requests;

use Flagship\Shipping\Flagship;
use FlagshipWoocommerce\FlagshipWoocommerceShipping;

class Validate_Token_Request extends Abstract_Flagship_Api_Request {

    public function __construct($token, $testEnv = 0)
    {
        $this->token = $token;
        $this->apiUrl = $this->getApiUrl($testEnv);
    }

    public function validateToken()
    {
        $apiClient = new Flagship($this->token, $this->apiUrl, 'woocommerce', FlagshipWoocommerceShipping::$version);
        try{
            $httpCode = $apiClient->validateTokenRequest($this->token)->execute();
            return $httpCode;
        } catch(\Exception $e){
            return $e->getMessage();
        }
    }
}
