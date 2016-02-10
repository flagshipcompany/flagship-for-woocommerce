<?php

class Wc_Admin_Post_Types_Flagship_Shipping_Pickup_Actions
{
    public static function output()
    {
        global $post_type;

        if ($post_type == 'shop_order') {
            ?>
            <script type="text/javascript">
            jQuery(function() {
                jQuery('<option>').val('flagship_shipping_pickup_schedule').text('<?php _e('Schedule pick-up', 'woocommerce')?>').appendTo('select[name="action"]');
                jQuery('<option>').val('flagship_shipping_pickup_schedule').text('<?php _e('Schedule pick-up', 'woocommerce')?>').appendTo('select[name="action2"]');
            });
            </script>
            <?php

        }
    }

    public static function save()
    {
        $wp_list_table = _get_list_table('WP_Posts_List_Table');
        $action = $wp_list_table->current_action();

        // Bail out if this is not a status-changing action
        if (strpos($action, 'flagship_shipping_') === false) {
            return;
        }

        console($action);
        die();

        $operation = substr($action, 18); // get the operation name from action

        if (!in_array($operation, array('pickup_schedule', 'pickup_void'))) {
            return;
        }

        $changed = 0;
        $post_ids = array_map('absint', (array) $_REQUEST['post']);
        $shippings = array();

        foreach ($post_ids as $post_id) {
            $order = wc_get_order($post_id);
            $shipment = get_post_meta($order->id, 'flagship_shipping_raw', true);

            $order->update_status($new_status, __('Order status changed by bulk edit:', 'woocommerce'), true);
            do_action('woocommerce_order_edit_status', $post_id, $new_status);
            ++$changed;
        }

        $sendback = add_query_arg(array('post_type' => 'shop_order', $report_action => true, 'changed' => $changed, 'ids' => implode(',', $post_ids)), '');

        if (isset($_GET['post_status'])) {
            $sendback = add_query_arg('post_status', sanitize_text_field($_GET['post_status']), $sendback);
        }

        wp_redirect(esc_url_raw($sendback));
        exit();
    }

    protected static function get_shippings_per_courier($post_ids)
    {
        $data = array(
            'fedex' => array(),
            'purolator' => array(),
            'ups' => array(),
        );

        foreach ($post_ids as $post_id) {
            $order = wc_get_order($post_id);
            $shipment = get_post_meta($order->id, 'flagship_shipping_raw', true);

            if (empty($shipment)) {
                continue;
            }

            $data[$shipment['service']['courier_name']][$order->id] = array(
                'shipment' => $shipment,
                'order' => $order,
            );
        }

        return $data;
    }
}
