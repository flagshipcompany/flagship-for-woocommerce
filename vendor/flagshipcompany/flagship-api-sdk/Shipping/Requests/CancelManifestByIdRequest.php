<?php
namespace Flagship\Shipping\Requests;

use Flagship\Apis\Requests\ApiRequest;
use Flagship\Apis\Exceptions\ApiException;
use Flagship\Shipping\Exceptions\CancelManifestByIdException;

class CancelManifestByIdRequest extends ApiRequest{

    public function __construct(string $apiToken,string $baseUrl,int $manifestId,string $flagshipFor,string $version){
        $this->apiToken = $apiToken;
        $this->apiUrl = $baseUrl.'/ship/edhl/'.$manifestId;
        $this->flagshipFor = $flagshipFor;
        $this->version = $version;
    }

    public function execute() : bool {
        try{    
            $cancelManifestRequest = $this->api_request($this->apiUrl,[],$this->apiToken,'DELETE',30,$this->flagshipFor,$this->version);
            $this->responseCode = $cancelManifestRequest["httpcode"];
            return $this->responseCode == 200 ? TRUE : FALSE;
        } catch (ApiException $e) {
            throw new CancelManifestByIdException($e->getMessage());
        }
    }

    public function getResponseCode() : ?int {
        if(isset($this->responseCode)){
            return $this->responseCode;
        }
        return NULL;
    }

}
