<?php

namespace Flagship\Shipping\Objects;

class Rate
{
    public function __construct( \stdClass $rate )
    {
        $this->rate = $rate;
    }

    public function getTotal() : float
    {
        return property_exists($this->rate,'price') ? $this->rate->price->total : 0.00;
    }

    public function getSubtotal() : float
    {
        return property_exists($this->rate,'price') ? $this->rate->price->subtotal : 0.00;
    }

    public function getTaxesTotal() : float
    {
        $total = 0.00;
        if(!property_exists($this->rate,'price')){
            return 0.00;
        }
        foreach ($this->rate->price->taxes as $key => $value) {
            $total += $value;
        }
        return $total;
    }

    public function getAdjustments() : ?string
    {
        return property_exists($this->rate,'price') ? $this->rate->price->adjustments : '';
    }

    public function getDebits() : ?string
    {
        return property_exists($this->rate,'price') ? $this->rate->price->debits : '';
    }

    public function getBrokerage() : ?string
    {
        return property_exists($this->rate,'price') ? $this->rate->price->brokerage : '';
    }

    public function getFlagshipCode() : string
    {
        return property_exists($this->rate,'service') ? $this->rate->service->flagship_code : '';
    }

    public function getTransitTime() : ?string
    {
        return $this->rate->service->transit_time;
    }

    public function getTaxesDetails() : array
    {
        if(!property_exists($this->rate,'price')){
            return [];
        }
        foreach ($this->rate->price->taxes as $key => $value) {
            $taxes[$key] = $value;
        }
        return $taxes;
    }

    public function getServiceCode() //Mixed return type
    {
        return property_exists($this->rate,'service') ? $this->rate->service->courier_code : '';
    }

    public function getDeliveryDate() : string
    {
        return property_exists($this->rate,'service') ? $this->rate->service->estimated_delivery_date : '';
    }

    public function getCourierDescription() : string
    {
        return property_exists($this->rate,'service') ? $this->rate->service->courier_desc : '';
    }

    public function getCourierName() : string
    {
        return property_exists($this->rate,'service') ? $this->rate->service->courier_name : '';
    }
}
