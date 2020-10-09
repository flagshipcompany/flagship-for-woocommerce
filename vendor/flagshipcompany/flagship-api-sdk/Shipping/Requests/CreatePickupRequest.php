<?php

namespace Flagship\Shipping\Requests;

use Flagship\Apis\Requests\ApiRequest;
use Flagship\Shipping\Objects\Pickup;
use Flagship\Apis\Exceptions\ApiException;
use Flagship\Shipping\Exceptions\CreatePickupException;


class CreatePickupRequest extends ApiRequest{
    protected $responseCode;
    public function __construct(string $baseUrl,string $token,array $pickupPayload, string $flagshipFor, string $version){
        $this->url = $baseUrl.'/pickups';
        $this->token = $token;
        $this->pickupPayload = $pickupPayload;
        $this->flagshipFor = $flagshipFor;
        $this->version = $version;
    }

    public function execute() : Pickup {
        try{
            $pickupRequest = $this->api_request($this->url,$this->pickupPayload,$this->token,'POST',30,$this->flagshipFor,$this->version);
            $pickupObject = count((array)$pickupRequest["response"]) == 0 ? new \stdClass() : $pickupRequest["response"]->content;
            $pickup = new Pickup($pickupObject);
            $this->responseCode = $pickupRequest["httpcode"];
            return $pickup;
        }
        catch(ApiException $e){
            throw new CreatePickupException($e->getMessage());
        }
    }

    public function getResponseCode() : ?int {
        if(isset($this->responseCode)){
            return $this->responseCode;
        }
        return NULL;
    }

}
