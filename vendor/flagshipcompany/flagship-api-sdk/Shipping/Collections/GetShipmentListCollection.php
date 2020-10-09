<?php

namespace Flagship\Shipping\Collections;

use Flagship\Shipping\Exceptions\GetShipmentListException;
use Flagship\Shipping\Objects\Shipment;
use Illuminate\Support\Collection;

class GetShipmentListCollection extends Collection{

    public function importShipments(array $shipments) : array {
        $allShipments = [];
        if(count($shipments) === 0){
            throw new GetShipmentListException('No shipments found');
        }
        foreach ($shipments as  $value) {
            $allShipments[] = new Shipment($value);
        }
        parent::__construct($allShipments);
        return $allShipments;
    }

    public function getById(int $id) : Shipment {
        $result = $this->where('shipment.id',$id);
        if($result->isEmpty()){
            throw new GetShipmentListException('No Shipments found for id : '.$id);
        }
        return $result->first();
    }

    public function getByTrackingNumber(string $trackingNumber) : Shipment {
        $result = $this->where('shipment.tracking_number',$trackingNumber);
        if($result->isEmpty()){
            throw new GetShipmentListException('No Shipments found for Tracking Number : '.$trackingNumber);
        }
        return $result->first();
    }

    public function getByStatus(string $status) : GetShipmentListCollection {
        $result = $this->filter(function($value,$key) use ($status){
            return strcasecmp($value->shipment->status,$status) === 0;
        });
        if($result->isEmpty()){
            throw new GetShipmentListException('No shipments found for Status : '.$status);
        }
        return $result;
    }

    public function getByPickupId(int $pickupId) : Shipment {
        $result = $this->where('shipment.pickup_id',$pickupId);
        if($result->isEmpty()){
            throw new GetShipmentListException('No shipments found for Pickup Id : '.$pickupId);
        }
        return $result->first();
    }

    public function getBySender(string $name) : GetShipmentListCollection {
        $result = $this->filter(function($value,$key) use ($name){
            return strcasecmp($value->shipment->from->attn,$name) === 0 ;
        });
        if($result->isEmpty()){
            throw new GetShipmentListException('No shipments found for Sender : '.$name);
        }
        return $result;
    }

    public function getByReceiver(string $name) : GetShipmentListCollection {
        $result = $this->filter(function($value,$key) use ($name){
            return strcasecmp($value->shipment->to->attn,$name) === 0;
        });
        if($result->isEmpty()){
            throw new GetShipmentListException('No shipments found for Receiver : '.$name);
        }
        return $result;
    }

    public function getByReference(string $reference) : GetShipmentListCollection {
        $result = $this->filter(function($value,$key) use ($reference){
            return strcasecmp($value->shipment->options->reference,$reference) === 0;
        });
        if($result->isEmpty()){
            throw new GetShipmentListException('No shipments found for Reference : '.$reference);
        }
        return $result;
    }

    public function getByDate(string $date) : GetShipmentListCollection {
        $result = $this->where('shipment.options.shipping_date',$date);
        if($result->isEmpty()){
            throw new GetShipmentListException('No shipments found for Shipping Date : '.$date);
        }
        return $result;
    }

    public function getByCourier(string $courier) : GetShipmentListCollection {
        $result = $this->filter(function($value,$key) use ($courier){
            return strcasecmp($value->shipment->service->courier_name,$courier) === 0;
        });
        if($result->isEmpty()){
            throw new GetShipmentListException('No shipments found for Courier : '.$courier);
        }
        return $result;
    }

    public function getBySenderPhone(string $phone) : GetShipmentListCollection {
        $result = $this->where('shipment.from.phone',$phone);
        if($result->isEmpty()){
            throw new GetShipmentListException('No shipments found for Sender\'s Phone : '.$phone);
        }
        return $result;
    }

    public function getByReceiverPhone(string $phone) : GetShipmentListCollection {
        $result = $this->where('shipment.to.phone',$phone);
        if($result->isEmpty()){
            throw new GetShipmentListException('No shipments found for Receiver\'s Phone : '.$phone);
        }
        return $result;
    }

    public function getBySenderCompany(string $company) : GetShipmentListCollection {
        $result = $this->filter(function($value,$key) use ($company){
            return strcasecmp($value->shipment->from->name,$company) === 0 ;
        });
        if($result->isEmpty()){
            throw new GetShipmentListException('No shipments found for Sender : '.$company);
        }
        return $result;
    }

    public function getByReceiverCompany(string $company) : GetShipmentListCollection{
        $result = $this->filter(function($value,$key) use ($company){
            return strcasecmp($value->shipment->to->name,$company) === 0 ;
        });
        if($result->isEmpty()){
            throw new GetShipmentListException('No shipments found for Receiver : '.$company);
        }
        return $result;

    }

}
