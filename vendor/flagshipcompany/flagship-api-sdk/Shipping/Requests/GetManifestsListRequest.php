<?php

namespace Flagship\Shipping\Requests;

use Flagship\Apis\Requests\ApiRequest;
use Flagship\Apis\Exceptions\ApiException;
use Flagship\Shipping\Exceptions\ManifestListException;
use Flagship\Shipping\Collections\ManifestListCollection;

class GetManifestsListRequest extends ApiRequest{

    public function __construct(string $token, string $baseUrl,string $flagshipFor,string $version){
        $this->apiToken = $token;
        $this->apiUrl = $baseUrl.'/ship/edhl/';
        $this->flagshipFor = $flagshipFor;
        $this->version = $version;
    }

    public function execute() : ManifestListCollection {
        try{
            $responseArray = $this->api_request($this->apiUrl,[],$this->apiToken,'GET',30,$this->flagshipFor,$this->version);
            $manifests = count((array)$responseArray["response"]) == 0 ? [] : $responseArray["response"]->content->records;
            $manifestList = new ManifestListCollection();
            $this->responseCode = $responseArray["httpcode"];
            $manifestList->importManifests($manifests);
            return $manifestList;
        } catch (ApiException $e){
            throw new ManifestListException($e->getMessage());
        }
    }

    public function getResponseCode() : ?int {
        if(isset($this->responseCode)){    
            return $this->responseCode;
        }
        return NULL;
    }   
}
