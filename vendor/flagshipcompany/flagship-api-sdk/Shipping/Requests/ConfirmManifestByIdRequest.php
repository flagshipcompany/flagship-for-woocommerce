<?php
namespace Flagship\Shipping\Requests;

use Flagship\Apis\Requests\ApiRequest;
use Flagship\Apis\Exceptions\ApiException;
use Flagship\Shipping\Exceptions\ConfirmManifestByIdException;
use Flagship\Shipping\Objects\Manifest;

class ConfirmManifestByIdRequest extends ApiRequest{

    public function __construct(string $token,string $baseUrl,int $manifestId,string $flagshipFor,string $version){
        $this->apiToken = $token;
        $this->apiUrl = $baseUrl.'/ship/edhl/close/'.$manifestId;
        $this->flagshipFor = $flagshipFor;
        $this->version = $version;
    }

    public function execute() : Manifest {
        try{    
            $responseArray = $this->api_request($this->apiUrl,[],$this->apiToken,'PUT',30,$this->flagshipFor,$this->version);
            $confirmedManifest = count((array)$responseArray["response"]) == 0 ? new \stdClass() : $responseArray["response"]->content;
            $manifest = new Manifest($confirmedManifest);
            $this->responseCode = $responseArray["httpcode"];
            return $manifest;
        } catch (ApiException $e){
            throw new ConfirmManifestByIdException($e->getMessage());
        }
    }

    public function getResponseCode() : ?int {
        if(isset($this->responseCode)){
            return $this->responseCode;
        }
        return NULL;
    }
}
