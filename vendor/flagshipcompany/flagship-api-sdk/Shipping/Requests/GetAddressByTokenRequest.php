<?php

namespace Flagship\Shipping\Requests;
use Flagship\Apis\Requests\ApiRequest;
use Flagship\Shipping\Objects\Address;
use Flagship\Apis\Exceptions\ApiException;
use Flagship\Shipping\Exceptions\GetAddressByTokenException;

class GetAddressByTokenRequest extends ApiRequest{
    public function __construct(string $apiToken,string $baseUrl,string $flagshipFor,string $version){
        $this->url = $baseUrl.'/addresses?is_hq=1';
        $this->token = $apiToken;
        $this->flagshipFor = $flagshipFor;
        $this->version = $version;
    }

    public function execute(){
        try{
            $getAddressRequest = $this->api_request($this->url,[],$this->token,'GET',10,$this->flagshipFor,$this->version);
            $addressObject = count((array)$getAddressRequest["response"]) == 0 ? [] : $getAddressRequest["response"]->content->records;
            $address = new Address(reset($addressObject));
            $this->responseCode = $getAddressRequest['httpcode'];
            return $address;
        } catch (ApiException $e){
            throw new GetAddressByTokenException($e->getMessage());
        }
    }

    public function getResponseCode() : ?int {
        if(isset($this->responseCode)){
            return $this->responseCode;
        }
        return NULL;
    }
} 
