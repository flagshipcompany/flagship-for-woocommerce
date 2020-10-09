<?php

namespace Flagship\Shipping\Requests;
use Flagship\Apis\Requests\ApiRequest;
use Flagship\Apis\Exceptions\ApiException;
use Flagship\Shipping\Exceptions\EditPickupException;
use Flagship\Shipping\Objects\Pickup;

class EditPickupRequest extends ApiRequest{

    protected $responseCode;

    public function __construct(string $baseUrl,string $token,array $payload,string $id, string $flagshipFor, string $version){

        $this->url = $baseUrl.'/pickups/'.$id;
        $this->token = $token;
        $this->editPickupPayload = $payload;
        $this->flagshipFor = $flagshipFor;
        $this->version = $version;
    }

    public function execute() : Pickup {
        try{
            $editPickupRequest = $this->api_request($this->url,$this->editPickupPayload,$this->token,'PUT',30,$this->flagshipFor,$this->version);
            $pickupObject = count((array)$editPickupRequest["response"]) == 0 ? new \stdClass() : $editPickupRequest["response"]->content;
            $editPickup = new Pickup($pickupObject);
            $this->responseCode = $editPickupRequest["httpcode"];
            return $editPickup;
        }
        catch(ApiException $e){
            throw new EditPickupException($e->getMessage());
        }
    }

    public function getResponseCode() : ?int {
        if(isset($this->responseCode)){
            return $this->responseCode;
        }
        return NULL;
    }
}
