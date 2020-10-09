<?php

namespace Flagship\Shipping\Exceptions;
use Flagship\Shipping\Exceptions\SmartshipException;

class ValidateTokenException extends SmartshipException{
    public function __construct(string $message, int $code=0){
        parent::__construct($message,$code);
        $this->message = 'Invalid Token. Returned with code: '.$this->code;
    }
}
