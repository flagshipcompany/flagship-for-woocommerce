<?php

namespace Flagship\Shipping\Requests;
use Flagship\Apis\Requests\ApiRequest;
use Flagship\Apis\Exceptions\ApiException;
use Flagship\Shipping\Exceptions\ValidateTokenException;

class ValidateTokenRequest extends ApiRequest{
    public function __construct(string $baseUrl,string $token, string $flagshipFor, string $version){
        $this->url = $baseUrl.'/check-token';
        $this->token = $token;
        $this->flagshipFor = $flagshipFor;
        $this->version = $version;
    }

    public function execute() : int {
        try{
            $response = $this->api_request($this->url,[],$this->token,'GET',30,$this->flagshipFor,$this->version);
            return $response["httpcode"];
        }
        catch(ApiException $e){
            throw new ValidateTokenException($e->getMessage(),$e->getCode());
        }
    }
}
