<?php

namespace FS\Components\Shipping\Object;

use FS\Injection\I;

class Shipment
{
    use RawDataAccessTrait;

    const STATUS_PREQUOTED = 1;
    const STATUS_CREATED = 2;

    protected $status = self::STATUS_PREQUOTED;
    protected $addresses = [];
    protected $shippingOptions = [];

    public function getId()
    {
        if ($this->isCreated() && isset($this->raw['shipment_id'])) {
            return $this->raw['shipment_id'];
        }
    }

    public function getCourier()
    {
        if (!$this->isCreated() || !isset($this->raw['service']['courier_name'])) {
            return;
        }

        return strtolower($this->raw['service']['courier_name']);
    }

    public function getToAddress()
    {
        return $this->addresses['to'];
    }

    public function getShippingOptions()
    {
        return $this->shippingOptions;
    }

    public function isPrequoted()
    {
        return $this->status == self::STATUS_PREQUOTED;
    }

    public function isCreated()
    {
        if (empty($this->raw)) {
            return false;
        }

        return empty($this->raw['exported']);
    }

    public function isExported()
    {
        if (empty($this->raw)) {
            return false;
        }

        return !empty($this->raw['exported']);
    }

    public function getExportedShipmentId()
    {
        if ($this->isExported() && isset($this->raw['flagship_id'])) {
            return $this->raw['flagship_id'];
        }
    }

    public function isFedexGround()
    {
        return $this->getCourier() == 'fedex' && (strpos($this->raw['service']['courier_code'], 'FedexGround') !== false);
    }

    public function isInternational()
    {
        return $this->addresses['to']['country'] != 'CA';
    }

    public function hasPickup()
    {
        return isset($this->raw['pickup']) && $this->raw['pickup'];
    }

    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function syncWithOrder(Order $order)
    {
        $raw = $order->getAttribute('flagship_shipping_raw');
        $raw = $raw ?: [];

        $this->raw = $raw;

        if ($this->raw) {
            $this->setStatus(self::STATUS_CREATED);
        }

        // make receiver address
        $this->addresses['to'] = [
            'name' => $order->native('shipping_company'),
            'attn' => $order->native('shipping_first_name').' '.$order->native('shipping_last_name'),
            'address' => trim($order->native('shipping_address_1').' '.$order->native('shipping_address_2')),
            'city' => $order->native('shipping_city'),
            'state' => $order->native('shipping_state'),
            'country' => $order->native('shipping_country'),
            'postal_code' => $order->native('shipping_postcode'),
            'phone' => $order->native('billing_phone'), // no such a field in the shipping!?
        ];

        $instanceOptionValue = $this->getShippingInstanceValue($order);

        if (!empty($instanceOptionValue) && isset($instanceOptionValue['signature_required']) && $instanceOptionValue['signature_required'] == 'yes') {
            $this->shippingOptions['signature_required'] = true;
        }

        return $this;
    }

    public function toArray()
    {
        return $this->raw;
    }

    public static function createFromOrder(Order $order)
    {
        $shipment = new self();

        return $shipment->syncWithOrder($order);
    }

    protected function getShippingInstanceValue(Order $order)
    {
        $package = [];
        $package['destination']['country'] = $order->native('shipping_country');
        $package['destination']['state'] = $order->native('shipping_state');
        $package['destination']['postcode'] = $order->native('shipping_postcode');

        $shippingZoneId = \WC_Shipping_Zones::get_zone_matching_package($package)->get_id();

        global $wpdb;

        $results = $wpdb->get_results(
            $wpdb->prepare("SELECT instance_id FROM {$wpdb->prefix}woocommerce_shipping_zone_methods WHERE zone_id = %s", $shippingZoneId)
        );

        if (empty($results)) {
            return;
        }

        $instanceId = array_column($results, 'instance_id')[0];
        $instanceOptionKey = 'woocommerce_'.(I::FLAGSHIP_SHIPPING_PLUGIN_ID).'_'.$instanceId.'_settings';
        $instanceOptionValue = \get_option($instanceOptionKey, null);

        return $instanceOptionValue;
    }
}
