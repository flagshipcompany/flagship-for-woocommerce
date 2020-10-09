<?php

namespace Flagship\Shipping\Objects;

class Service
{
    public function __construct( \stdClass $service )
    {
        $this->service = $service;
    }

    public function getCode() : string {
        return property_exists($this->service,'courier_code') ? $this->service->courier_code : '';
    }

    public function getDescription() : string {
        return property_exists($this->service,'courier_description') ? $this->service->courier_description : '';
    }

    public function getFlagshipCode() : string {
        return property_exists($this->service,'flagship_code') ? $this->service->flagship_code : '';
    }
}
