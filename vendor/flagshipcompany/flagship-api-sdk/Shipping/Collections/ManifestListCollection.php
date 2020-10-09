<?php

namespace Flagship\Shipping\Collections;

use Illuminate\Support\Collection;
use Flagship\Shipping\Objects\Manifest;
use Flagship\Shipping\Exceptions\ManifestException;
use Flagship\Shipping\Exceptions\ManifestListException;

class ManifestListCollection extends Collection{

    public function importManifests(array $manifests){
        if(count($manifests) == 0){
            throw new ManifestException('No manifests available');
        }

        foreach ($manifests as $key => $value) {
            $allManifests[] = new Manifest($value);
        }
        parent::__construct($allManifests);
        return $allManifests;
    }

    public function getByStatus(string $status) : ?ManifestListCollection {
        if(strcasecmp($status,'prequoted') != 0 && strcasecmp($status,'confirmed') != 0 && strcasecmp($status,'cancelled') != 0 ){
            return NULL;
        }

        $result = $this->filter(function($value,$key) use ($status){
            return strcasecmp($value->manifest->status,$status) === 0;
        });
        if($result->isEmpty()){
            throw new ManifestListException('No manifests found for Status : '.$status);
        }
        return $result;
    }

}
