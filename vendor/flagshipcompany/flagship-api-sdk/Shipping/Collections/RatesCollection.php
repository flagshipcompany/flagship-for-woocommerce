<?php

namespace Flagship\Shipping\Collections;
use Flagship\Shipping\Objects\Rate;
use Flagship\Shipping\Exceptions\QuoteException;
use Illuminate\Support\Collection;

class RatesCollection extends Collection{

    public function importRates(array $rates) : array {
        if(count($rates) == 0){
            throw new QuoteException('No quotes available');
        }

        foreach ($rates as $key => $value) {
            $allRates[] = new Rate($value);
        }
        parent::__construct($allRates);
        return $allRates;
    }

    public function getCheapest() : Rate {
        return $this->sortByPrice()->first();
    }

    public function getFastest() : Rate {

        return $this->sortByTime()->first();
    }

    public function getByCourier(string $courier) : RatesCollection  {
       
        $couriers = $this->filter(function($value,$key) use ($courier){
            return strcasecmp($value->rate->service->courier_name,$courier) === 0;
        });

        if ($couriers->isEmpty()) {
             throw new QuoteException('No rates for courier - '. $courier);
        }

        return $couriers;
    }

    public function sortByPrice() : RatesCollection {

        $sorted = $this->sortBy(function($value,$key){
            return $value->rate->price->total;
        });
        
        return $sorted;
    }

    public function sortByTime() : RatesCollection {

        $sorted = $this->sortBy(function($value){
            return $value->rate->service->estimated_delivery_date;
        });

        return $sorted;
    }

}