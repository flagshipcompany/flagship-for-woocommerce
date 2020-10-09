<?php

namespace Flagship\Shipping\Objects;

class Packing{
    public function __construct(\stdClass $packing){
        $this->packing = $packing;
    }

    public function getBoxModel() : string {
        return property_exists($this->packing,'box_model') ? $this->packing->box_model : '';
    }

    public function getLength() : string {
        return property_exists($this->packing,'length') ? $this->packing->length : '';
    }

    public function getWidth() : string {
        return property_exists($this->packing,'width') ? $this->packing->width : '';
    }

    public function getHeight() : string {
        return property_exists($this->packing,'box_model') ? $this->packing->height : '';
    }

    public function getWeight() : int {
        return property_exists($this->packing,'weight') ? $this->packing->weight : '';
    }

    public function getItems() : array {
        return property_exists($this->packing,'items') ? $this->packing->items : [];
    }
}
