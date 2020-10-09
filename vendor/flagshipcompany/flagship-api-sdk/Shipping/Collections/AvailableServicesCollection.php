<?php
namespace Flagship\Shipping\Collections;
use Illuminate\Support\Collection;
use Flagship\Shipping\Objects\Service;
use Flagship\Shipping\Exceptions\AvailableServicesException;

class AvailableServicesCollection extends Collection{

    public function importServices(array $services){
        if(count($services) == 0){
            throw new AvailableServicesException('No services available');
        }

        foreach ($services as $key => $value) {
            $allServices[] = new Service($value);
        }
        parent::__construct($allServices);
        return $allServices;
    }

    public function getServicesByCourier(string $courier) : AvailableServicesCollection {
        $services = $this->filter(function($value,$key) use($courier){
            return stripos($value->service->courier_description,$courier) !== FALSE;
        });

        if($services->isEmpty()){
            throw new AvailableServicesException('No services for courier - '.$courier);
        }

        return $services;
    }

    public function getStandardServices() : AvailableServicesCollection {
        $services = $this->filter(function($value,$key) {
            return stripos($value->service->flagship_code,'standard') !== FALSE;
        });

        if($services->isEmpty()){
            throw new AvailableServicesException('No services for courier - '.$courier);
        }

        return $services;
    }

    public function getOvernightServices() : AvailableServicesCollection {
        $services = $this->filter(function($value,$key) {
            return stripos($value->service->courier_description,'overnight') !== FALSE;
        });

        if($services->isEmpty()){
            throw new AvailableServicesException('No services for courier - '.$courier);
        }

        return $services;
    }

    public function getExpressServices()  : AvailableServicesCollection {
        $services = $this->filter(function($value,$key) {
            return stripos($value->service->courier_description,'overnight') === FALSE && stripos($value->service->flagship_code,'standard') === FALSE;
        });

        if($services->isEmpty()){
            throw new AvailableServicesException('No services for courier - '.$courier);
        }

        return $services;
    }

    public function getStandardServicesByCourier(string $courier): AvailableServicesCollection {
        $services = $this->getStandardServices()->filter(function($value,$key) use ($courier) {
            return stripos($value->service->courier_description,$courier) !== FALSE;
        });

        if($services->isEmpty()){
            throw new AvailableServicesException('No services for courier - '.$courier);
        }

        return $services;
    }

    public function getOvernightServicesByCourier(string $courier): AvailableServicesCollection {
        $services = $this->getOvernightServices()->filter(function($value,$key) use ($courier) {
            return stripos($value->service->courier_description,$courier) !== FALSE;
        });

        if($services->isEmpty()){
            throw new AvailableServicesException('No services for courier - '.$courier);
        }

        return $services;
    }

    public function getExpressServicesByCourier(string $courier): AvailableServicesCollection {
        $services = $this->getExpressServices()->filter(function($value,$key) use ($courier) {
            return stripos($value->service->courier_description,$courier) !== FALSE;
        });

        if($services->isEmpty()){
            throw new AvailableServicesException('No services for courier - '.$courier);
        }

        return $services;
    }


}
