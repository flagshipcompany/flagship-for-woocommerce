<?php
namespace Flagship\Shipping\Objects;

class Pickup{

    public function __construct(\stdClass $pickup){
        $this->pickup = $pickup;
    }

    public function getId() : int {
        return $this->pickup->id;
    }

    public function getConfirmation() : ?string {
        return property_exists($this->pickup,'confirmation') ? $this->pickup->confirmation : NULL ;
    }

    public function getFullAddress() : array {
        $address = property_exists($this->pickup,'address') ? json_decode(json_encode($this->pickup->address),TRUE) : [];
        return $address;
    }

    public function getAddress() : string {
        return property_exists($this->pickup,'address') ? $this->pickup->address->address : '' ;
    }

    public function getCompany() : string {
        return property_exists($this->pickup,'address') ? $this->pickup->address->name : '';
    }

    public function getName() : string {
        return property_exists($this->pickup,'address') ? $this->pickup->address->attn : '';
    }

    public function getSuite() : string {
        return  property_exists($this->pickup,'address') ? (is_null($this->pickup->address->suite) ? '' : $this->pickup->address->suite) : '';
    }

    public function getCity() : string {
        return property_exists($this->pickup,'address') ? $this->pickup->address->city : '';
    }

    public function getCountry() : string {
        return property_exists($this->pickup,'address') ? $this->pickup->address->country : '';
    }

    public function getState() : string {
        return property_exists($this->pickup,'address') ? $this->pickup->address->state : '';
    }

    public function getPostalCode() : string {
        return property_exists($this->pickup,'address') ? $this->pickup->address->postal_code : '';
    }
    public function getPhone() : string {
        return property_exists($this->pickup,'address') ? $this->pickup->address->phone : '';
    }
    public function getPhoneExt() : string {
        return property_exists($this->pickup,'address') ? (is_null($this->pickup->address->ext) ? '' : $this->pickup->address->ext) : '';
    }

    public function isAddressCommercial() : ?bool {
        return property_exists($this->pickup,'address') ? ($this->pickup->address->is_commercial ? TRUE : FALSE) : NULL;
    }

    public function getCourier() : string {
        return property_exists($this->pickup,'courier') ? $this->pickup->courier : '';
    }

    public function getUnits() : string {
        return property_exists($this->pickup,'units') ? $this->pickup->units : '';
    }

    public function getBoxes() : string {
        return property_exists($this->pickup,'boxes') ? $this->pickup->boxes : '';
    }

    public function getWeight() : ?string {
        return $this->pickup->weight;
    }

    public function getPickupLocation() : ?string {
        return $this->pickup->location;
    }

    public function getDate() : ?string {
        return is_null($this->pickup->date) ? NULL : $this->pickup->date;
    }

    public function getFromTime() : string {
        return $this->pickup->from;
    }

    public function getUntilTime() : string {
        return $this->pickup->until;
    }

    public function getToCountry() : string {
        return $this->pickup->to_country;
    }

    public function getInstructions() : ?string {
        return is_null($this->pickup->instruction) ? NULL : $this->pickup->instruction ;
    }

    public function isCancelled() : bool {
        return ($this->pickup->cancelled) ? TRUE : FALSE ;
    }
}
