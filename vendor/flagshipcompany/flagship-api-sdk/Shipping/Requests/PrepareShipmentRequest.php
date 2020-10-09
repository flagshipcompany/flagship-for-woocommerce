<?php
namespace Flagship\Shipping\Requests;

use Flagship\Apis\Requests\ApiRequest;
use Flagship\Shipping\Objects\Shipment;
use Flagship\Apis\Exceptions\ApiException;
use Flagship\Shipping\Exceptions\PrepareShipmentException;

class PrepareShipmentRequest extends ApiRequest{
    protected $responseCode;

    public function __construct(string $baseUrl, string $token, array $payload, string $flagshipFor, string $version){

        $this->url = $baseUrl.'/ship/prepare';
        $this->token = $token;
        $this->payload = $payload;
        $this->flagshipFor = $flagshipFor;
        $this->version = $version;
    }

    public function execute() : ?Shipment  {
        try{
            $prepareShipmentRequest = $this->api_request($this->url,$this->payload,$this->token,'POST',30,$this->flagshipFor,$this->version);
            $responseObject = count((array)$prepareShipmentRequest["response"]) == 0 ? new \stdClass() : $prepareShipmentRequest["response"]->content ;
            $prepareShipment = new Shipment($responseObject);
            $this->responseCode = $prepareShipmentRequest["httpcode"];
            return $prepareShipment;
        }
        catch(ApiException $e){
            throw new PrepareShipmentException($e->getMessage());
        }
    }

    public function getResponseCode() : ?int {
        if(isset($this->responseCode)){
            return $this->responseCode;
        }
        return NULL;
    }
}
