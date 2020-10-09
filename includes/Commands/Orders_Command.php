<?php
namespace FlagshipWoocommerce\Commands;

use FlagshipWoocommerce\FlagshipWoocommerceShipping;
use FlagshipWoocommerce\Helpers\Export_Order_Helper;
use FlagshipWoocommerce\Requests\Export_Order_Request;

class Orders_Command {

    protected $export_helper;

    public function __construct() {
        $plugin_settings = get_option(FlagshipWoocommerceShipping::getSettingsOptionKey());
        $this->export_helper = new Export_Order_Helper($plugin_settings);
    }

    /**
    * Export orders to FlagShip.
    *
    * ## OPTIONS
    *
    * [<order_id>]
    * : The order id
    *
    * [--all]
    * : Export all the orders under processing that have not been exported.
    *
    * ## EXAMPLES
    *
    *     # Export a specific order.
    *     $ wp fcs orders export [order_id]
    *     # Export all orders.
    *     $ wp fcs orders export [--all]
    */
    public function export($args, $assoc_args) {
        if (!$this->args_valid($args, $assoc_args)) {
            \WP_CLI::error('Invalid arguments');

            return;
        }

        if (isset($args[0])) {
            $this->export_by_id($args[0]);

            return;
        }

        $this->export_all();
    }

    protected function args_valid($args, $assoc_args) {
        return (isset($args[0]) || isset($assoc_args['all'])) && !(isset($args[0]) && isset($assoc_args['all']));
    }

    protected function export_by_id($order_id) {
        $order = wc_get_order($order_id);

        if (!$order) {
            \WP_CLI::error('Order does not exist!');

            return;
        }

        if (!$this->validate_shipping_address($order)) {
            \WP_CLI::error('Cannot export this order since the shipping address is incomplete or invalid');

            return;
        }

        $this->export_helper->set_order($order);

        try{
            $shipmentId = $this->export_helper->exportOrder();
        }
        catch(\Exception $e){
            \WP_CLI::error($e->getMessage());

            return;    
        }

        \WP_CLI::success(sprintf('Order has been exported to FlagShip with shipment id %d', $shipmentId));

        return $shipmentId;
    }

    protected function export_all() {
        $unexported_orders = $this->list_eligible_orders('processing');
        \WP_CLI::line(sprintf('%d orders will be exported to FlagShip', count($unexported_orders)));

        if (count($unexported_orders) == 0) {
            return;
        }

        $number_exported = 0;

        foreach ($unexported_orders as $key => $order) {
            \WP_CLI::line(sprintf('Exporting order: %d', $order->get_id()));
            $shipment_id = $this->export_by_id($order->get_id());
            $number_exported += ($shipment_id ? 1 : 0);
        }

        \WP_CLI::line(sprintf('%d orders were exported', $number_exported));
    }

    protected function list_eligible_orders($status = null) {
        $filters = $status ? array('status' => $status) : array();
        $orders = wc_get_orders($filters);

        $unexported_orders = array_filter($orders, function($order) {
            if (!$this->validate_shipping_address($order)) {
                return false;
            }

            $this->export_helper->set_order($order);

            return empty($this->export_helper->getShipmentIdFromOrder($order->get_id()));
        });

        return $unexported_orders;
    }

    protected function validate_shipping_address($order) {
        return (new Export_Order_Request(null))->isOrderShippingAddressValid($order);
    }
}