<?php
namespace Flagship\Shipping\Exceptions;

class SmartshipException extends \Exception{

    protected function getErrors() : array {

        if(empty($this->message)){

            $this->message = 'Unable to connect to FlagShip - Code : '. $this->getCode();
            $errorsArray = [ $this->message ];
            return $errorsArray;
        }

        if(!$this->isJson($this->message)){
            $this->message = parent::getMessage();
            $errorsArray = [ $this->message ];
            return $errorsArray;
        }

        if(is_string($this->message) && is_array(json_decode($this->message,TRUE)) && count(json_decode($this->message,TRUE)) == 1){
            $this->message = $this->removeArrayBracketsIfExist();
            $errorsArray = [$this->message];
            return $errorsArray;
        }

        $errors = json_decode($this->message,TRUE)["errors"];

        if(is_null($errors)){
            $errorsArray = [ $this->message ];
            return $errorsArray;
        }

        if(is_string($errors)){
            $errorsArray = [ $errors ];
            return $errorsArray;
        }

        if(count($errors) === 1){
            $errorsArray[] = $this->normalizeErrors($errors);
            return $errorsArray;
        }

        $errorsArray = [];
        $i = 0;
        $keys = array_keys($errors);

        foreach($errors as $error){

            $errorsArray[] = $keys[$i]." : ".$this->normalizeErrors($error);
            $i++;
        }
        return $errorsArray;

    }

    protected function removeArrayBracketsIfExist(){
        return trim($this->message,'["]');
    }

    protected function normalizeErrors(array $error) : string {
        $errorMsg = '';
        while(!is_string($error)){
            $errorMsg .= is_string(key($error)) ? key($error)." : " : "";
            $error = reset($error);
        }
        $errorMsg .= $error;
        return $errorMsg;
    }

    protected function isJson(string $string) : bool {
        json_decode($string);
        return (json_last_error() === JSON_ERROR_NONE);
    }

    protected function ifErrors() : bool {
        $errors = json_decode($this->message,TRUE)["errors"];

        if(is_null($errors)){
            return FALSE;
        }
        return TRUE;
    }

    protected function getResponseErrorsByType($type) : array {
        $errors = json_decode($this->message,TRUE)[$type];

        if(is_string($errors)){
            return [ $errors ];
        }

        $responseErrorMsg = [];

        while(!is_string($errors)){
            $responseErrorMsg[] = is_string(key($errors)) ? key($errors)." : " : "";
            $errors = reset($errors);
        }

        $responseErrorMsg[] = $errors;
        return $responseErrorMsg;
    }

    protected function getErrorsByCode() : array {
        if($this->code === 404){
            return ['Requested shipment not found'];
        }
    }

}
