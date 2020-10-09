<?php

namespace Flagship\Apis\Requests;
use Flagship\Apis\Exceptions\ApiException;
use Flagship\Shipping\Exceptions\FilterException;

abstract class ApiRequest{

    public function setStoreName(string $storeName){
        $this->setHeader("X-Store-Name",$storeName);
        return $this;
    }
    public function setOrderId(int $orderId){
        $this->setHeader("X-Order-Id",$orderId);
        return $this;
    }
    public function setOrderLink(string $orderLink){
        $this->setHeader("X-Order-Link",$orderLink);
        return $this;
    }

    protected function api_request(string $url,array $json,string $apiToken,string $method, int $timeout, string $flagshipFor="", string $version="") : array

    {
        $curl = curl_init();

        $this->setSmartshipToken($apiToken);
        $this->setContentType();
        $this->setAppName($flagshipFor);

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS  => json_encode($json),
            CURLOPT_HTTPHEADER => $this->headers
            ];

        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl,CURLINFO_HTTP_CODE);

        $responseArray = [
            "response"  => json_decode($response),
            "httpcode"  => $httpcode
        ];

        curl_close($curl);

        if(($httpcode >= 400 && $httpcode < 600) || ($httpcode === 0) || ($response === false) || ($httpcode === 209)){
            throw new ApiException($response,$httpcode);
        }

        return $responseArray;
    }

    protected function addRequestFilter($key,$value){

        if(in_array($key,$this->filters)){
            $this->url = $this->url.'?'.$key.'='.$value;
            return $this;
        }
        throw new FilterException("Invalid filter argument provided");
    }

    protected function setSmartshipToken(string $token){
        $this->setHeader("X-Smartship-Token",$token);
        return $this;
    }
    protected function setContentType(){
        $this->setHeader("Content-Type","application/json");
        return $this;
    }
    protected function setAppName(string $appName){
        $this->setHeader("X-App-Name",$appName);
        return $this;
    }

    private function setHeader(string $key, string $value){
        $this->headers[] = $key.": ".$value;
        return $this; 
    }

}
