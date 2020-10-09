<?php
namespace FlagshipWoocommerce\Helpers;

use FlagshipWoocommerce\Requests\Export_Order_Request;

class Export_Order_Helper {

	public static $shipmentIdField = 'flagship_shipping_shipment_id';

	protected $order;

    protected $pluginSettings;

    protected $errorCodes = array(
        'shipment_exists' => 401,
        'token_missing' => 402,
    );

    public function __construct($pluginSettings) {
        $this->pluginSettings = $pluginSettings;
    }

    public function __call($method, $arguments) {
        if (!$this->order) {
            throw new \Exception('Order not set!');
        }

        return call_user_func_array(array($this, $method), $arguments);
    }

    public function set_order($order) {
        $this->order = $order;
    }
    
    public function getShipmentIdFromOrder($orderId) {
        $orderMeta = get_post_meta($orderId);

        if (!isset($orderMeta[self::$shipmentIdField])) {
            return;
        }

        return reset($orderMeta[self::$shipmentIdField]);
    }

    public function exportOrder() {
        if ($this->getShipmentIdFromOrder($this->order->get_id())) {
            throw new \Exception(__('This order has already been exported to FlagShip', 'flagship-for-woocommerce'), $this->errorCodes['shipment_exists']);
        }

        $token = get_array_value($this->pluginSettings, 'token');

        if (!$token) {
            throw new \Exception(__('FlagShip API token is missing', 'flagship-for-woocommerce'), $this->errorCodes['token_missing']);
        }

        $apiRequest = new Export_Order_Request($token);
        $exportedShipment = $apiRequest->exportOrder($this->order, $this->pluginSettings);

        if ($exportedShipment) {
            update_post_meta($this->order->get_id(), self::$shipmentIdField, $exportedShipment->getId());

            return $exportedShipment->getId();
        }
    }
}