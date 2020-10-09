<?php

namespace Flagship\Shipping\Requests;

use Flagship\Apis\Requests\ApiRequest;
use Flagship\Apis\Exceptions\ApiException;
use Flagship\Shipping\Exceptions\AssociateToDepotException;

class AssociateToDepotRequest extends ApiRequest{

    public function __construct(string $apiToken,string $baseUrl,int $manifestId,array $payload,string $flagshipFor,string $version){
        $this->apiToken = $apiToken;
        $this->apiUrl = $baseUrl.'/ship/edhl/to-depot/'.$manifestId;
        $this->payload = $payload;
        $this->flagshipFor = $flagshipFor;
        $this->version = $version;
    }

    public function execute() : bool {
        try{
            $responseArray = $this->api_request($this->apiUrl,$this->payload,$this->apiToken,'PATCH',30,$this->flagshipFor,$this->version);
            $this->responseCode = $responseArray["httpcode"];
            return $this->responseCode == 204 ? TRUE : FALSE;
        } catch (ApiException $e){
            throw new AssociateToDepotException($e->getMessage());
        }
    }

    public function getResponseCode() : ?int {
        if(isset($this->responseCode)){
            return $this->responseCode;
        }
        return NULL;
    }

}
