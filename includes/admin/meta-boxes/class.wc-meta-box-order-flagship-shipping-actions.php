<?php

class WC_Meta_Box_Order_Flagship_Shipping_Actions
{
    /**
     * Output the metabox.
     *
     * @param WP_Post $post
     */
    public static function output($post)
    {
        global $post, $thepostid, $theorder;

        if (!is_int($thepostid)) {
            $thepostid = $post->ID;
        }

        if (!is_object($theorder)) {
            $theorder = wc_get_order($thepostid);
        }

        $order = $theorder;

        $service = Flagship_Request_Formatter::get_flagship_shipping_service($order);
        $shipment = get_post_meta($thepostid, 'flagship_shipping_raw', true);

        if ($shipment) {
            $payload = array(
                'type' => 'created',
                'shipment' => $shipment
            );
        } elseif ($service['provider'] == FLAGSHIP_SHIPPING_PLUGIN_ID) {
            $payload = array(
                'type' => 'create',
                'service' => $service
            );
        } else {
            $payload = array('type' => 'unavailable');
        }

        Flagship_View::render('meta-boxes/order-flagship-shipping-actions', $payload);
    }

    /**
     * Save meta box data.
     *
     * @param int     $post_id
     * @param WP_Post $post
     */
    public static function save($post_id, $post)
    {
        $order = wc_get_order($post_id);
        $shipment = get_post_meta($post_id, 'flagship_shipping_raw', true);

        // in case 
        if (empty($shipment)) {
            $flagship = Flagship_Application::get_instance();

            $response = $flagship->client()->post(
                '/ship/confirm',
                Flagship_Request_Formatter::get_confirmation_request($order)
            );

            $shipping = $response->get_content();

            if ($shipping['errors']) {
                return;
            }

            update_post_meta($post_id, 'flagship_shipping_shipment_id', $shipping['content']['shipment_id']);
            update_post_meta($post_id, 'flagship_shipping_shipment_tracking_number', $shipping['content']['tracking_number']);
            update_post_meta($post_id, 'flagship_shipping_courier_name', $shipping['content']['service']['courier_name']);
            update_post_meta($post_id, 'flagship_shipping_courier_service_code', $shipping['content']['service']['courier_code']);
            update_post_meta($post_id, 'flagship_shipping_raw', $shipping['content']);
            
            return;
        }

        $shipment_id = sanitize_text_field($_POST['flagship_shipping_void_shipment_id']);

        if (empty($shipment_id) || $shipment_id != $shipment['shipment_id']) {
            return;
        }

        $flagship = Flagship_Application::get_instance();
        $response = $flagship->client()->delete('/ship/shipments/'.$shipment_id);

        if ($response->get_code() == 204) {
            delete_post_meta($post_id, 'flagship_shipping_raw');
        }



    }
}
