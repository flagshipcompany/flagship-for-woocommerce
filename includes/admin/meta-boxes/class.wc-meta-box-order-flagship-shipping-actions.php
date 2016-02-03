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
                'shipment' => $shipment,
            );
        } elseif ($service['provider'] == FLAGSHIP_SHIPPING_PLUGIN_ID) {
            $payload = array(
                'type' => 'create',
                'service' => $service,
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

        // confirm if not shipment exists
        if (empty($shipment)) {
            return self::shipment_confirm($order);
        }

        $shipment_id = sanitize_text_field($_POST['flagship_shipping_shipment_id']);

        if (empty($shipment_id) || $shipment_id != $shipment['shipment_id']) {
            return;
        }

        $shipment_action = sanitize_text_field($_POST['flagship_shipping_shipment_action']);
        $flagship = Flagship_Application::get_instance();

        if ($shipment_action == 'shipment-void') {
            self::shipment_void($order, $shipment_id);
        } elseif ($shipment_action == 'pickup-schedule') {
            $pickup_date = sanitize_text_field($_POST['flagship_shipping_pickup_schedule_date']);
            self::pickup_schedule($order, $shipment, $pickup_date);
        } elseif ($shipment_action == 'pickup-void') {
            self::pickup_void($order, $shipment);
        }
    }

    protected static function shipment_confirm($order)
    {
        $flagship = Flagship_Application::get_instance();

        $response = $flagship->client()->post(
            '/ship/confirm',
            Flagship_Request_Formatter::get_confirmation_request($order)
        );

        $shipping = $response->get_content();

        if ($shipping['errors']) {
            return;
        }

        update_post_meta($order->id, 'flagship_shipping_shipment_id', $shipping['content']['shipment_id']);
        update_post_meta($order->id, 'flagship_shipping_shipment_tracking_number', $shipping['content']['tracking_number']);
        update_post_meta($order->id, 'flagship_shipping_courier_name', $shipping['content']['service']['courier_name']);
        update_post_meta($order->id, 'flagship_shipping_courier_service_code', $shipping['content']['service']['courier_code']);
        update_post_meta($order->id, 'flagship_shipping_raw', $shipping['content']);
    }

    protected function shipment_void($order, $shipment_id)
    {
        $flagship = Flagship_Application::get_instance();

        $response = $flagship->client()->delete('/ship/shipments/'.$shipment_id);

        console($response->get_code());

        if ($response->get_code() == 204 || $response->get_code() == 209) {
            delete_post_meta($order->id, 'flagship_shipping_raw');
        }
    }

    protected function pickup_schedule($order, $shipment, $date)
    {
        $flagship = Flagship_Application::get_instance();

        $request = Flagship_Request_Formatter::get_single_pickup_schedule_request($order, $shipment, $date);

        $response = $flagship->client()->post(
            '/pickups',
            $request
        );

        if ($response->is_success()) {
            $shipment['pickup'] = $response->get_content()['content'];

            update_post_meta($order->id, 'flagship_shipping_raw', $shipment);
        }
    }

    protected function pickup_void($order, $shipment)
    {
        $flagship = Flagship_Application::get_instance();

        $response = $flagship->client()->delete('/pickups/'.$shipment['pickup']['id']);

        if ($response->get_code() == 204) {
            unset($shipment['pickup']);
            update_post_meta($order->id, 'flagship_shipping_raw', $shipment);
        }
    }
}
