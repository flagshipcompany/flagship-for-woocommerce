<?php
namespace Flagship\Shipping\Requests;

use Flagship\Apis\Requests\ApiRequest;
use Flagship\Shipping\Objects\Shipment;
use Flagship\Apis\Exceptions\ApiException;
use Flagship\Shipping\Exceptions\ConfirmShipmentByIdException;

class ConfirmShipmentByIdRequest extends ApiRequest{
    protected $responseCode;
    public function __construct(string $baseUrl, string $token, int $id, string $flagshipFor, string $version){
        $this->url = $baseUrl.'/ship/'.$id.'/confirm';
        $this->token = $token;
        $this->flagshipFor = $flagshipFor;
        $this->version = $version;
    }

    public function execute() : Shipment {
        try{
            $confirmShipmentRequest = $this->api_request($this->url,[],$this->token,'PUT',30,$this->flagshipFor,$this->version);

            $confirmShipmentObject = count((array)$confirmShipmentRequest["response"]) == 0 ? new \stdClass() : $confirmShipmentRequest["response"]->content;

            $confirmShipment = new Shipment($confirmShipmentObject);
            $this->responseCode = $confirmShipmentRequest["httpcode"];
            return $confirmShipment;
        }
        catch(ApiException $e){
            throw new ConfirmShipmentByIdException($e->getMessage());
        }
    }

    public function getResponseCode() : ?int {
        if(isset($this->responseCode)){
            return $this->responseCode;
        }
        return NULL;
    }
}
