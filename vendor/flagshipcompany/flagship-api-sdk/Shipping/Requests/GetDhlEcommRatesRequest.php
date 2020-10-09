<?php

namespace Flagship\Shipping\Requests;

use Flagship\Apis\Requests\ApiRequest;
use Flagship\Apis\Exceptions\ApiException;
use Flagship\Shipping\Collections\RatesCollection;
use Flagship\Shipping\Exceptions\GetDhlEcommRatesException;

class GetDhlEcommRatesRequest extends ApiRequest{

    public function __construct(string $token,string $baseUrl,array $payload,string $flagshipFor,string $version){
        $this->token = $token;
        $this->url = $baseUrl.'/ship/edhl/rates';
        $this->payload = $payload;
        $this->flagshipFor = $flagshipFor;
        $this->version = $version;
    }

    public function execute() : RatesCollection {
        try{
            $responseArray = $this->api_request($this->url,$this->payload,$this->token,'POST',30,$this->flagshipFor,$this->version);
            $responseObject = count((array)$responseArray["response"]) == 0 ? [] : $responseArray["response"]->content;
            $rates = new RatesCollection();
            $rates->importRates($responseObject);
            $this->responseCode = $responseArray["httpcode"];
            return $rates;
        } catch(ApiException $e){
            throw new GetDhlEcommRatesException($e->getMessage());
        }
    }

    public function getResponseCode() : ?int {
        if(isset($this->responseCode)){
            return $this->responseCode;
        }
        return NULL;
    }

}