<?php

namespace Flagship\Shipping\Requests;

use Flagship\Apis\Requests\ApiRequest;
use Flagship\Shipping\Objects\Shipment;
use Flagship\Apis\Exceptions\ApiException;
use Flagship\Shipping\Exceptions\ConfirmShipmentException;

class ConfirmShipmentRequest extends ApiRequest{
    protected $responseCode;
    public function __construct(string $baseUrl, string $token, array $payload, string $flagshipFor, string $version){
        $this->url = $baseUrl.'/ship/confirm';
        $this->token = $token;
        $this->payload = $payload;
        $this->flagshipFor = $flagshipFor;
        $this->version = $version;
    }

    public function execute() : Shipment {
        try{
            $confirmShipmentRequest = $this->api_request($this->url,$this->payload,$this->token,'POST',30,$this->flagshipFor,$this->version);

            $confirmShipmentObject = count((array)$confirmShipmentRequest["response"]) == 0 ? new \stdClass() : $confirmShipmentRequest["response"]->content;

            $confirmShipment = new Shipment($confirmShipmentObject);
            $this->responseCode = $confirmShipmentRequest["httpcode"];
            return $confirmShipment;
        }
        catch(ApiException $e){
            throw new ConfirmShipmentException($e->getMessage());
        }
    }

    public function getResponseCode() : ?int {
        if(isset($this->responseCode)){
            return $this->responseCode;
        }
        return NULL;
    }
}
