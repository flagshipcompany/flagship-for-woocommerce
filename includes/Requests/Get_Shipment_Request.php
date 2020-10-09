<?php
namespace FlagshipWoocommerce\Requests;

use Flagship\Shipping\Flagship;
use FlagshipWoocommerce\FlagshipWoocommerceShipping;

class Get_Shipment_Request extends Abstract_Flagship_Api_Request {

    public function __construct($token, $testEnv=0)
    {
    	$this->token = $token;
    	$this->apiUrl = $this->getApiUrl($testEnv);
    }

    public function getShipmentById($id)
    {
        $apiClient = new Flagship($this->token, $this->apiUrl, 'woocommerce', FlagshipWoocommerceShipping::$version);
        try{
            $shipment = $apiClient->getShipmentByIdRequest($id)->execute();
            return $shipment;
        } catch(\Exception $e){
            return $e->getMessage();
        }
    }
}
