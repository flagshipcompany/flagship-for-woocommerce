<?php
namespace Flagship\Shipping\Requests;
use Flagship\Apis\Requests\ApiRequest;
use Flagship\Apis\Exceptions\ApiException;
use Flagship\Shipping\Exceptions\CancelPickupException;

class CancelPickupRequest extends ApiRequest{
    public function __construct(string $baseUrl,string $token,int $id, string $flagshipFor, string $version){
        $this->url = $baseUrl.'/pickups/'.$id;
        $this->token = $token;
        $this->flagshipFor = $flagshipFor;
        $this->version = $version;
    }

    public function execute() : bool {
        try{
            $cancelPickupRequest = $this->api_request($this->url,[],$this->token,'DELETE',30,$this->flagshipFor,$this->version);
            $this->responseCode = $cancelPickupRequest["httpcode"];
            return $cancelPickupRequest["httpcode"] == 200 ? TRUE : FALSE;
        }
        catch(ApiException $e){
            throw new CancelPickupException($e->getMessage());
        }
    }

    public function getResponseCode() : ?int {
        if(isset($this->responseCode)){
            return $this->responseCode;
        }
        return NULL;
    }
}
