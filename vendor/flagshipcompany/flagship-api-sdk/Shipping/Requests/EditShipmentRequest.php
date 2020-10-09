<?php
namespace Flagship\Shipping\Requests;

use Flagship\Apis\Requests\ApiRequest;
use Flagship\Apis\Exceptions\ApiException;
use Flagship\Shipping\Objects\Shipment;
use Flagship\Shipping\Exceptions\EditShipmentException;

class EditShipmentRequest extends Apirequest{

    protected $responseCode;

    public function __construct(string $baseUrl,string $token,array $payload,string $shipmentId, string $flagshipFor, string $version){
        $this->url = $baseUrl.'/ship/shipments/'.$shipmentId;
        $this->token = $token;
        $this->payload = $payload;
        $this->flagshipFor = $flagshipFor;
        $this->version = $version;
    }

    public function execute() : Shipment {
        try{
            $editShipmentRequest = $this->api_request($this->url,$this->payload,$this->token,'PUT',30,$this->flagshipFor,$this->version);
            $editShipmentObject = count((array)$editShipmentRequest["response"]) == 0 ? new \stdClass() : $editShipmentRequest["response"]->content;
            $editShipment = new Shipment($editShipmentObject);
            $this->responseCode = $editShipmentRequest["httpcode"];
            return $editShipment;
        }
        catch(ApiException $e){
            throw new EditShipmentException($e->getMessage());
        }
    }

    public function getResponseCode() : ?int {
        if(isset($this->responseCode)){
            return $this->responseCode;
        }
        return NULL;
    }

}
