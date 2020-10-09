<?php
namespace Flagship\Shipping\Collections;

use Flagship\Shipping\Objects\Packing;
use Flagship\Shipping\Exceptions\PackingException;
use Illuminate\Support\Collection;

class PackingCollection extends Collection{
    public function importPackages(array $packages) : array {
        if(count($packages) === 0){
            throw new QuoteException('No packages available');
        }

        foreach ($packages as $key => $value) {
            $allPackages[] = new Packing($value);
        }
        parent::__construct($allPackages);
        return $allPackages;
    }
}
