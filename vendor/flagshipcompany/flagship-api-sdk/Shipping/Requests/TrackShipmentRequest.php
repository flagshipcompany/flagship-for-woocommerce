<?php

namespace Flagship\Shipping\Requests;
use Flagship\Apis\Requests\ApiRequest;
use Flagship\Apis\Exceptions\ApiException;
use Flagship\Shipping\Exceptions\TrackShipmentException;
use Flagship\Shipping\Objects\TrackShipment;

class TrackShipmentRequest extends ApiRequest{

    public function __construct(string $baseUrl,string $token,int $id, string $flagshipFor, string $version){
        $this->url= $baseUrl.'/ship/track?shipment_id='.$id;
        $this->token = $token;
        $this->flagshipFor = $flagshipFor;
        $this->version = $version;
    }

    public function execute() {
        try{
            $trackShipment = $this->api_request($this->url,[],$this->token,'GET',30,$this->flagshipFor,$this->version);
            $this->responseCode = $trackShipment["httpcode"];
            $trackingObject = count((array)$trackShipment["response"]) == 0 ? new \stdClass() : $trackShipment["response"]->content ;
            return new TrackShipment($trackingObject);
        }
        catch(ApiException $e){
            throw new TrackShipmentException($e->getMessage(),$e->getCode());
        }
    }
    public function getResponseCode() : int {
        return $this->responseCode;
    }
}
