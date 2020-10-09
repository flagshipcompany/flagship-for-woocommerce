<?php

namespace Flagship\Shipping\Exceptions;
use Flagship\Shipping\Exceptions\SmartshipException;

class CancelShipmentException extends SmartshipException{

    public function __construct(string $message, int $code=0){
       
       parent::__construct($message,$code);
       
       $this->message = (!$this->ifErrors()) ? implode("\n",$this->getResponseErrorsByType("notices")): $this->message = implode("\n",$this->getResponseErrorsByType("errors"));
    }
}