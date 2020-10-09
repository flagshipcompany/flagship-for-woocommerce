<?php

namespace Flagship\Shipping\Requests;

use Flagship\Apis\Requests\ApiRequest;
use Flagship\Shipping\Objects\Shipment;
use Flagship\Shipping\Exceptions\GetShipmentByIdException;
use Flagship\Apis\Exceptions\ApiException;

class GetShipmentByIdRequest extends ApiRequest{

    protected $responseCode;
    public function __construct(string $baseUrl,string $token,string $flagshipFor,string $version,int $id){
        $this->url = $baseUrl.'/ship/shipments/'.$id;
        $this->token = $token;
        $this->flagshipFor = $flagshipFor;
        $this->version = $version;
    }

    public function execute() : Shipment {
        try{
            $response = $this->api_request($this->url,[],$this->token,"GET",10,$this->flagshipFor,$this->version);
            $responseObject = count((array)$response["response"]) == 0 ? new \stdClass() : $response["response"]->content;
            $shipment = new Shipment($responseObject);
            $this->responseCode = $response["httpcode"];
            return $shipment;
        } catch(ApiException $e){
            throw new GetShipmentByIdException($e->getMessage());
        }
    }

    public function getResponseCode() : ?int {
        if(isset($this->responseCode)){
            return $this->responseCode;
        }
        return NULL;
    }
}
