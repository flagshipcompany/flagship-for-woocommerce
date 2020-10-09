<?php
namespace Flagship\Shipping\Objects;

class Address{
    public function __construct(\stdClass $address){
        $this->address = $address;
    }

    public function getId() : int {
        return $this->address->id;
    }

    public function getName() : string {
        return $this->address->name;
    }

    public function getAttn() : ?string {
        return $this->address->attn;
    }

    public function getAddress() : string {
        return $this->address->address;
    }

    public function getSuite() : ?string {
        return $this->address->suite;
    }

    public function getDepartment() : ?string {
        return $this->address->department;
    }

    public function getIsCommercial() : bool {
        return $this->address->is_commercial == 1 ? TRUE : FALSE;
    }

    public function getCity() : string {
        return $this->address->city;
    }

    public function getCountry() : string {
        return $this->address->country;
    }

    public function getState() : string {
        return $this->address->state;
    }

    public function getPostalCode() : string {
        return $this->address->postal_code;
    }

    public function getPhone() : string {
        return $this->address->phone;
    }

    public function getPhoneExt() : ?string {
        return $this->address->phone_ext;
    }

    public function getEmail() : string {
        return $this->address->email;
    }

    public function getCompanyId() : int {
        return $this->address->company_id;
    }

    public function getGroupId() : int {
        return $this->address->group_id;
    }

    public function getTaxCode() : ?string {
        return $this->address->tax_code;
    }

    public function getIsHq() : bool {
        return $this->address->is_hq == 1 ? TRUE : FALSE; 
    }

    public function getIsBilling() : bool {
        return $this->address->is_billing == 1 ? TRUE : FALSE;
    }

    public function getIsPickup() : bool {
        return $this->address->is_pickup == 1 ? TRUE : FALSE;
    }

    public function getShippingAccount() : ?string {
        return $this->address->shipping_account;
    }
}
