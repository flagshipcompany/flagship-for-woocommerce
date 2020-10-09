<?php
namespace FlagshipWoocommerce\Helpers;

use FlagshipWoocommerce\Order_Action_Processor;

class Order_Filter_Helper {

    protected $filter_name = '_exported_flagship';

    public function filter_orders_by_coupon_used() {
        global $typenow;

    	if ('shop_order' === $typenow) {
            Template_Helper::render_php('_order_filter.php', array(
                'value' => isset($_GET['_exported_flagship']) ? $_GET['_exported_flagship'] : '',
                'fieldName' => $this->filter_name,
            ));
    	}
    }

    public function add_filterable_where($where) {
        global $typenow, $wpdb;

        if ('shop_order' === $typenow && isset($_GET[$this->filter_name] ) && ! empty( $_GET[$this->filter_name])) {
            $templ = " AND (
                SELECT COUNT(*)
                FROM {$wpdb->prefix}postmeta
                WHERE meta_key = '%s'
                AND meta_value IS NOT NULL
                AND post_id = {$wpdb->posts}.ID
            )";
            $templ .= $_GET[$this->filter_name] == "yes" ? " > 0" : " = 0";
            $clause = sprintf($templ, Order_Action_Processor::$shipmentIdField);
            $where .= $clause;
        }

        return $where;
    }
}
