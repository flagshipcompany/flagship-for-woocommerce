<?php

namespace Flagship\Shipping\Requests;

use Flagship\Apis\Requests\ApiRequest;
use Flagship\Apis\Exceptions\ApiException;
use Flagship\Shipping\Exceptions\GetShipmentListException;
use Flagship\Shipping\Collections\GetShipmentListCollection;
use Flagship\Shipping\Exceptions\FilterException;

class GetShipmentListRequest extends ApiRequest{

    protected $responseCode;
    protected $filters;
    public function __construct(string $baseUrl,string $token, string $flagshipFor, string $version) {
        $this->token = $token;
        $this->url = $baseUrl . '/ship/shipments';
        $this->flagshipFor = $flagshipFor;
        $this->version = $version;
        $this->filters = [
                    'courier',
                    'status',
                    'reference',
                    'tracking_number',
                    'package_pin',
                    'page',
                    'limit'
                ];
    }

    public function execute() : GetShipmentListCollection {
        try{
            $request = $this->api_request($this->url,[],$this->token,"GET",30,$this->flagshipFor,$this->version);
            $shipmentRecords = count((array)$request["response"]) == 0 ? [] : $request["response"]->content->records;
            $shipments = new GetShipmentListCollection();
            $shipments->importShipments($shipmentRecords);
            $this->responseCode = $request["httpcode"];
            return $shipments;
        }
        catch(ApiException $e){
            throw new GetShipmentListException($e->getMessage());
        }

    }

    public function getResponseCode() : ?int {
        if(isset($this->responseCode)){
            return $this->responseCode;
        }
        return NULL;
    }

    public function addFilter($key,$value) : GetShipmentListRequest {
        try{
            return $this->addRequestFilter($key,$value);
        }catch(FilterException $e){
            throw new GetShipmentListException($e->getMessage(),$e->getCode());
        }
    }

}
