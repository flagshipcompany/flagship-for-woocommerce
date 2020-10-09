<?php

namespace Flagship\Shipping\Objects;

use Flagship\Shipping\Objects\Package;


class Shipment{

    public function __construct(\stdclass $shipment){
        $this->shipment = $shipment;
    }

    public function getId() : int {
        if(property_exists($this->shipment, 'shipment_id')){
            return $this->shipment->shipment_id;
        }
        return $this->shipment->id;
    }


    public function getTrackingNumber() : ?string {
        return property_exists($this->shipment, 'tracking_number') ? $this->shipment->tracking_number : NULL ;
    }

    public function getStatus() : ?string {
        return property_exists($this->shipment, 'status') ? $this->shipment->status : NULL;
    }

    public function getPickupId() : ?string {
        return property_exists($this->shipment, 'pickup_id') ? $this->shipment->pickup_id : NULL ;
    }

    public function getSenderCompany() : ?string {
        return property_exists($this->shipment, 'from') ? $this->shipment->from->name : NULL ;
    }

    public function getSenderName() : ?string {
        return property_exists($this->shipment, 'from') ? $this->shipment->from->attn : NULL;
    }

    public function getSenderAddress() : ?string {
        return property_exists($this->shipment, 'from') ? $this->shipment->from->address : NULL;
    }

    public function getSenderSuite() : ?string {
        return property_exists($this->shipment, 'from') ? $this->shipment->from->suite : NULL;
    }

    public function getSenderDepartment() : ?string {
        return property_exists($this->shipment, 'from') ? ((property_exists($this->shipment->from, 'department')) ? $this->shipment->from->department : NULL) : NULL;
    }

    public function getSenderCity() : ?string {
        return property_exists($this->shipment, 'from') ? $this->shipment->from->city : NULL;
    }

    public function getSenderCountry() : ?string {
        return property_exists($this->shipment, 'from') ? $this->shipment->from->country : NULL;
    }

    public function getSenderState() : ?string {
        return property_exists($this->shipment, 'from') ? $this->shipment->from->state : NULL;
    }

    public function getSenderPostalCode() : ?string {
        return property_exists($this->shipment, 'from') ? $this->shipment->from->postal_code : NULL;
    }

    public function getSenderPhone() : ?string {
        return property_exists($this->shipment, 'from') ? $this->shipment->from->phone : NULL;
    }

    public function getSenderPhoneExt() : ?string {
        return property_exists($this->shipment, 'from') ? $this->shipment->from->phone_ext : NULL;
    }

    public function getSenderDetails() : ?array {

        $sender = property_exists($this->shipment, 'from') ?json_decode(json_encode($this->shipment->from),TRUE) : NULL ;
        return $sender;
    }

    public function getReceiverCompany() : ?string {
        return property_exists($this->shipment, 'to') ? $this->shipment->to->name : NULL;
    }

    public function getReceiverName() : ?string {
        return property_exists($this->shipment, 'to') ? $this->shipment->to->attn : NULL;
    }

    public function getReceiverAddress() : ?string {
        return property_exists($this->shipment, 'to') ? $this->shipment->to->address : NULL;
    }

    public function getReceiverSuite() : ?string {
        return property_exists($this->shipment, 'to') ? $this->shipment->to->suite : NULL;
    }

    public function getReceiverDepartment() : ?string {
        return property_exists($this->shipment, 'to') ? (property_exists($this->shipment->to, 'department') ? $this->shipment->to->department  : NULL ) : NULL;
    }

    public function IsReceiverCommercial() : ?bool {
        return property_exists($this->shipment, 'to') ? ($this->shipment->to->is_commercial ?  TRUE : FALSE) : NULL;
    }

    public function getReceiverCity() : ?string {
        return property_exists($this->shipment, 'to') ? $this->shipment->to->city : NULL;
    }

    public function getReceiverCountry() : ?string {
        return property_exists($this->shipment, 'to') ? $this->shipment->to->country : NULL;
    }

    public function getReceiverState() : ?string {
        return property_exists($this->shipment, 'to') ? $this->shipment->to->state : NULL;
    }

    public function getReceiverPostalCode() : ?string {
        return property_exists($this->shipment, 'to') ? $this->shipment->to->postal_code : NULL;
    }

    public function getReceiverPhone() : ?string {
        return property_exists($this->shipment, 'to') ? $this->shipment->to->phone : NULL;
    }

    public function getReceiverPhoneExt() : ?string {
        return property_exists($this->shipment, 'to') ? $this->shipment->to->phone_ext : NULL;
    }

    public function getReceiverDetails() : ?array {
        $receiver = property_exists($this->shipment, 'to') ?json_decode(json_encode($this->shipment->to),TRUE) : NULL;

        return $receiver;
    }

    public function getReference() : ?string {
        return property_exists($this->shipment->options, 'reference') ? $this->shipment->options->reference : NULL ;
    }

    public function getDriverInstructions() : ?string {

        return property_exists($this->shipment, 'options') ? (property_exists($this->shipment->options, 'driver_instructions') ? $this->shipment->options->driver_instructions : NULL) : NULL;

    }

    public function isSignatureRequired() : ?bool {
         return property_exists($this->shipment, 'options') ? (property_exists($this->shipment->options, 'signature_required')? ($this->shipment->options->signature_required ? TRUE : FALSE) : NULL ) : NULL;
    }

    public function getShippingDate() : ?string {
        return property_exists($this->shipment, 'options') ? (property_exists($this->shipment->options, 'shipping_date') ?$this->shipment->options->shipping_date : NULL) : NULL ;
    }

    public function getTrackingEmails() : ?string {
        return property_exists($this->shipment, 'options') ? (property_exists($this->shipment->options, 'tracking_emails') ?  $this->shipment->options->tracking_emails : NULL) : NULL;
    }

    public function getInsuranceValue() : ?float {
        return property_exists($this->shipment, 'options') ? (property_exists($this->shipment->options, 'insurance') ? $this->shipment->options->insurance->value : NULL) : NULL;
    }

    public function getInsuranceDescription() : ?string {
        return property_exists($this->shipment, 'options') ? (property_exists($this->shipment->options, 'insurance') ? $this->shipment->options->insurance->description : NULL) : NULL;
    }

    public function getCodMethod() : ?string {
        return property_exists($this->shipment, 'options') ? (property_exists($this->shipment->options, 'cod') ? $this->shipment->options->cod->method :NULL) : NULL;
    }

    public function getCodPayableTo() : ?string {
        return property_exists($this->shipment, 'options') ? (property_exists($this->shipment->options, 'cod') ? $this->shipment->options->cod->payable_to :NULL) : NULL ;
    }

    public function getCodReceiverPhone() : ?string {
        return property_exists($this->shipment, 'options') ? (property_exists($this->shipment->options, 'cod') ? $this->shipment->options->cod->receiver_phone :NULL) : NULL;
    }


    public function getCodAmount() : ?float {
        return property_exists($this->shipment, 'options') ? (property_exists($this->shipment->options, 'cod') ? $this->shipment->options->cod->amount :NULL) : NULL;
    }

    public function getCodCurrency() : ?string {
        return property_exists($this->shipment, 'options') ? (property_exists($this->shipment->options, 'cod') ? $this->shipment->options->cod->currency :NULL) : NULL;
    }

    public function IsSaturdayDelivery() : ?bool {
        return property_exists($this->shipment, 'options') ? (property_exists($this->shipment->options, 'saturday_delivery') ? ( $this->shipment->options->saturday_delivery ? TRUE : FALSE ) : NULL ) : NULL;
    }

    public function getCourierCode() : ?string {
        return $this->shipment->service->courier_code;
    }

    public function getCourierDescription() : ?string {
        return $this->shipment->service->courier_desc;
    }

    public function getCourierName() : ?string {
        return $this->shipment->service->courier_name;
    }

    public function getEstimatedDeliveryDate() : ?string {
        return $this->shipment->service->estimated_delivery_date;
    }

    public function getPackages() : Package{
        return new Package(json_decode(json_encode($this->shipment->packages),TRUE));
    }

    public function getPackageContent() : ?string {
        if(is_array($this->shipment->packages)){
            return NULL;
        }
        return $this->shipment->packages->content;
    }

    public function getPackageUnits() : ?string {
        if(is_array($this->shipment->packages)){
            return NULL;
        }
        return $this->shipment->packages->units;
    }

    public function getPackageType() : ?string {
        if(is_array($this->shipment->packages)){
            return NULL;
        }
         return $this->shipment->packages->type;
    }

    public function getItemsDetails() : array {
        $items = [];
        if(is_object($this->shipment->packages)){
            return $this->shipment->packages->items;
        }

        foreach ($this->shipment->packages as $item) {
            $items[] = $item;
        }
        return $items;
    }

    public function getSubtotal() : ?float {
        if(property_exists($this->shipment, 'subtotal')){
            return $this->shipment->subtotal;
        }

        if(property_exists($this->shipment,'price')){
            return $this->shipment->price->subtotal;
        }
        return NULL;
    }

    public function getTotal() : ?float {
        if(property_exists($this->shipment, 'total')){
            return $this->shipment->total;
        }

        if(property_exists($this->shipment,'price')){
            return $this->shipment->price->total;
        }
        return NULL;
    }

    public function getTaxesDetails() : ?array {
        if(property_exists($this->shipment, 'taxes')){
            return json_decode(json_encode($this->shipment->taxes),TRUE);
        }

        if(property_exists($this->shipment,'price')){
            return json_decode(json_encode($this->shipment->price->taxes),TRUE);
        }
        return NULL;
    }

    public function getTaxesTotal() : float {
            $sum = 0.00;
            $taxes = property_exists($this->shipment, 'taxes') ? $this->shipment->taxes : $this->shipment->price->taxes;

            if(is_null($taxes)){
                return $sum;
            }

            foreach ($taxes as $tax) {
                $sum += $tax;
            }

            return $sum;
    }

    public function getCharges() : ?array{
        return json_decode(json_encode($this->shipment->price->charges),TRUE);
    }

    public function getAdjustments() : ?array{
        $adjustments = property_exists($this->shipment, 'adjustments') ? $this->shipment->adjustments : $this->shipment->price->adjustments;
        return $adjustments;
    }

    public function getDebits() : ?array{
        $debits = property_exists($this->shipment, 'debits') ? $this->shipment->debits : $this->shipment->price->debits;
        return $debits;
    }

    public function getLabel() : ?string {
        $label =  property_exists($this->shipment, 'documents') ? $this->shipment->documents->regular_label : NULL;
        $label = property_exists($this->shipment, 'labels') ? $this->shipment->labels->regular : $label;
        return $label;
    }

    public function getThermalLabel() : ?string {

        $thermalLabel =  property_exists($this->shipment, 'documents') ? $this->shipment->documents->thermal_label : NULL;
        $thermalLabel = property_exists($this->shipment, 'labels') ? $this->shipment->labels->thermal : $thermalLabel;
        return $thermalLabel;
    }

    public function getCommercialInvoice() : ?string {
        return property_exists($this->shipment, 'documents') ? ( property_exists($this->shipment->documents, 'commercial_invoice') ? $this->shipment->documents->commercial_invoice : NULL) : NULL;
    }

    public function getTransitDetails() : ?array {
        return property_exists($this->shipment,'transit_details') ? $this->shipment->transit_details : NULL;
    }

    public function isDocumentsOnly() : ?bool {
        return property_exists($this->shipment, 'documents_only') ? ($this->shipment->documents_only ? TRUE : FALSE ) : NULL;
    }

    public function getFlagshipCode() : ?string {
        return property_exists($this->shipment->service,'flagship_code') ?$this->shipment->service->flagship_code : NULL;
    }

    public function getTransitTime() : ?string {
        return property_exists($this->shipment->service, 'transit_time') ? $this->shipment->service->transit_time : NULL ;
    }
}
