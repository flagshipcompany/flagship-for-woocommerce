<?php

namespace Flagship\Shipping\Objects;

use Flagship\Shipping\Objects\Shipment;
use Flagship\Shipping\Objects\Rate;
use Flagship\Shipping\Collections\RatesCollection;

class Manifest{

    public function __construct(\stdClass $manifest){
        $this->manifest = $manifest;
    }

    public function getName() : string {
        return $this->manifest->name;
    }

    public function getStatus() : string {
        return $this->manifest->status;
    }

    public function getId() : int {
        return $this->manifest->id;
    }

    public function getToDepotShipment() : ?Shipment {
        if(property_exists($this->manifest, 'to_depot_shipment')){
            return new Shipment($this->manifest->to_depot_shipment);
        }
        return NULL;
    }

    public function getShipmentIds() : ?array {
        if(property_exists($this->manifest, 'shipment_ids')){
            return $this->manifest->shipment_ids;
        }
        return NULL;
    }

    public function getPriceByShipmentId(int $shipmentId) : ?Rate {
        if(property_exists($this->manifest, 'prices')){
            return new Rate($this->manifest->prices->$shipmentId);
        }
        return NULL;
    }

    public function getAllPrices() : ?RatesCollection {
        if(property_exists($this->manifest, 'prices')){
            $prices = new RatesCollection();
            $prices->importRates((array)$this->manifest->prices);
            return $prices;
        }
        return NULL;
    }

    public function getSubtotal() : ?float {
        if(property_exists($this->manifest, 'totals')){
            return $this->manifest->totals->subtotal;
        }
        return NULL;
    }

    public function getTotal() : ?float {
        if(property_exists($this->manifest, 'totals')){
            return $this->manifest->totals->total;
        }
        return NULL;
    }

    public function getTaxesDetails() : ?array {
        if(property_exists($this->manifest, 'totals')){
            return json_decode(json_encode($this->manifest->totals->taxes),TRUE);
        }
        return NULL;
    } 

    public function getTaxesTotal() : ?float {
        if(!property_exists($this->manifest, 'totals')){
            return NULL;
        }
        $taxes = $this->manifest->totals->taxes;
        $total = 0.00;
        foreach ($taxes as $tax) {
            $total += $tax;
        }
        return $total;
    }

    public function getToDepotId() : ?int {
        if(property_exists($this->manifest, 'to_depot_id')){
            return $this->manifest->to_depot_id;
        }
        return NULL;
    }

    public function getBolNumber() : ?int {
        if(property_exists($this->manifest,'bol_number')){
            return $this->manifest->bol_number;
        }
        return NULL;
    }

    //returns regular labels
    public function getShipmentsLabels() : ?string { 
        if(property_exists($this->manifest, 'documents')){
            return property_exists($this->manifest->documents,'labels') ? $this->manifest->documents->labels->regular : $this->manifest->documents->regular_label;
        }
        return NULL;
    }

    public function getShipmentsThermalLabels() : ?string { 
        if(property_exists($this->manifest, 'documents')){
            return property_exists($this->manifest->documents,'labels') ? $this->manifest->documents->labels->thermal : $this->manifest->documents->thermal_label;
        }
        return NULL;
    }

    public function getManifestSummary() : ?string { 
        if(property_exists($this->manifest, 'documents')){
            return property_exists($this->manifest->documents,'manifest') ? $this->manifest->documents->manifest : NULL;
        }
        return NULL;
    }

    //returns regular label
    public function getToDepotLabel() : ?string { 
        if(property_exists($this->manifest, 'documents')){
            return property_exists($this->manifest->documents,'to_depot') ? $this->manifest->documents->to_depot->regular : NULL;
        }
        return NULL;
    }

    public function getToDepotThermalLabel() : ?string {
        if(property_exists($this->manifest, 'documents')){
            return property_exists($this->manifest->documents, 'to_depot') ? $this->manifest->documents->to_depot->thermal : NULL;
        }
        return NULL;
    }

}