<?php

namespace Flagship\Shipping\Objects;

class TrackShipment {

    public function __construct(?\stdClass $trackShipment){
        $this->trackShipment = $trackShipment;
    }

    public function getCurrentStatus() : string {
        return property_exists($this->trackShipment,'current_status') ? $this->trackShipment->current_status : '';
    }

    public function getShipmentId() : int {
        return property_exists($this->trackShipment,'shipment_id') ? $this->trackShipment->shipment_id : '';
    }

    public function getStatusDescription() : string {
        return property_exists($this->trackShipment,'status_desc') ? $this->trackShipment->status_desc : '';
    }

    public function getCourierUpdate() : ?string{
        return property_exists($this->trackShipment,'courier_update') ? $this->trackShipment->courier_update : '';
    }
}
