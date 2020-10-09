<?php

namespace Flagship\Shipping\Requests;
use Flagship\Apis\Requests\ApiRequest;
use Flagship\Shipping\Collections\GetPickupListCollection;
use Flagship\Apis\Exceptions\ApiException;
use Flagship\Shipping\Exceptions\GetPickupListException;
use Flagship\Shipping\Exceptions\FilterException;

class GetPickupListRequest extends ApiRequest{

    protected $responseCode;
    protected $filters;
    public function __construct(string $baseUrl,string $token, string $flagshipFor, string $version){
        $this->url = $baseUrl.'/pickups';
        $this->token = $token;
        $this->flagshipFor = $flagshipFor;
        $this->version = $version;
        $this->filters =[
                            'courier',
                            'date',
                            'page',
                            'limit'
                        ];
    }

    public function execute() : GetPickupListCollection {
        try{
            $getPickupListRequest = $this->api_request($this->url,[],$this->token,'GET',10,$this->flagshipFor,$this->version);
            $getPickupListRecords = count((array)$getPickupListRequest["response"]) == 0 ? [] : $getPickupListRequest["response"]->content->records;
            $pickupList = new GetPickupListCollection();
            $pickupList->importPickups($getPickupListRecords);
            $this->responseCode = $getPickupListRequest["httpcode"];
            return $pickupList;
        }
        catch(ApiException $e){
            throw new GetPickupListException($e->getMessage());
        }
    }

    public function getResponseCode() : ?int {
        if(isset($this->responseCode)){
            return $this->responseCode;
        }
        return NULL;
    }

    public function addFilter($key,$value) : GetPickupListRequest {
        try{
            return $this->addRequestFilter($key,$value);
        }catch(FilterException $e){
            throw new GetPickupListException($e->getMessage(),$e->getCode());
        }
    }
}
