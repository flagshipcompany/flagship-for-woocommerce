<?php

class WC_Meta_Box_Order_Flagship_Shipping_Actions
{
    public static function register()
    {
        add_meta_box(
            'wc-flagship-shipping-box',
            __('Flagship', 'flagship-shipping'),
            array(__CLASS__, 'output'),
            'shop_order',
            'side',
            'high'
        );
    }

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
        $requote_rates = get_post_meta($thepostid, 'flagship_shipping_requote_rates', true);

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

        if ($requote_rates) {
            $payload['requote_rates'] = $requote_rates;
        }

        $flagship = Flagship_Application::get_instance();

        $flagship->notification->scope('shop_order', array('id' => $order->id));
        $flagship->notification->view();

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
        $action = sanitize_text_field($_POST['flagship_shipping_shipment_action']);

        switch ($action) {
            case 'shipment-create':
                self::shipment_confirm($order);
                break;
            case 'shipment-void':
                self::shipment_void($order);
                break;
            case 'shipment-requote':
                self::shipment_requote($order);
                break;
            case 'pickup-schedule':
                self::pickup_schedule($order);
                break;
            case 'pickup-void':
                self::pickup_void($order);
                break;
        }
    }

    protected static function shipment_confirm($order)
    {
        $flagship = Flagship_Application::get_instance();
        $flagship->notification->scope('shop_order', array('id' => $order->id));
        $shipment = get_post_meta($post_id, 'flagship_shipping_raw', true);

        if ($shipment) {
            $flagship->notification->add('warning', 'You have flagship shipment for this order. SmartshipID ('.$shipment['shipment_id'].')');

            return;
        }

        $overload_shipping_method = isset($_POST['flagship_shipping_service']) ? sanitize_text_field($_POST['flagship_shipping_service']) : null;
        $request = Flagship_Request_Formatter::get_confirmation_request($order, $overload_shipping_method);

        $response = $flagship->client()->post(
            '/ship/confirm',
            $request
        );

        $shipping = $response->get_content();

        if ($shipping['errors']) {
            $flagship->notification->add('error', $shipping['errors']);

            return;
        }

        delete_post_meta($order->id, 'flagship_shipping_requote_rates');

        update_post_meta($order->id, 'flagship_shipping_shipment_id', $shipping['content']['shipment_id']);
        update_post_meta($order->id, 'flagship_shipping_shipment_tracking_number', $shipping['content']['tracking_number']);
        update_post_meta($order->id, 'flagship_shipping_courier_name', $shipping['content']['service']['courier_name']);
        update_post_meta($order->id, 'flagship_shipping_courier_service_code', $shipping['content']['service']['courier_code']);
        update_post_meta($order->id, 'flagship_shipping_raw', $shipping['content']);
    }

    protected static function shipment_requote($order)
    {
        $flagship = Flagship_Application::get_instance();

        $request = Flagship_Request_Formatter::get_requote_request($order);
        $response = $flagship->client()->post(
            '/ship/rates',
            $request
        );

        if (!$response->is_success()) {
            $flagship->notification->scope('shop_order', array('id' => $order->id));
            $flagship->notification->add('error', 'Unable to requote. Code '.$response->get_code());

            return;
        }

        $rates = Flagship_Request_Formatter::get_requote_rates_options(
            $response->get_content()['content']
        );

        update_post_meta($order->id, 'flagship_shipping_requote_rates', $rates);
    }

    protected function shipment_void($order)
    {
        $flagship = Flagship_Application::get_instance();
        $flagship->notification->scope('shop_order', array('id' => $order->id));
        $shipment = get_post_meta($order->id, 'flagship_shipping_raw', true);

        $shipment_id = self::get_accessible_shipment_id($shipment);

        if (!$shipment_id) {
            return;
        }

        $response = $flagship->client()->delete('/ship/shipments/'.$shipment_id);

        if ($response->get_code() == 204 || $response->get_code() == 209) {
            self::pickup_void($order);
            delete_post_meta($order->id, 'flagship_shipping_raw');
        }
    }

    protected function pickup_schedule($order)
    {
        $flagship = Flagship_Application::get_instance();
        $flagship->notification->scope('shop_order', array('id' => $order->id));
        $shipment = get_post_meta($order->id, 'flagship_shipping_raw', true);

        $shipment_id = self::get_accessible_shipment_id($shipment);

        if (!$shipment_id) {
            return;
        }

        $shipping = array(
            'order' => $order,
            'shipment' => $shipment,
            'date' => $date,
        );

        $request = Flagship_Request_Formatter::get_single_pickup_schedule_request($shipping);
        $response = $flagship->client()->post(
            '/pickups',
            $request
        );

        if ($response->is_success()) {
            $shipment['pickup'] = $response->get_content()['content'];

            update_post_meta($order->id, 'flagship_shipping_raw', $shipment);
        }
    }

    protected function pickup_void($order)
    {
        $flagship = Flagship_Application::get_instance();
        $flagship->notification->scope('shop_order', array('id' => $order->id));
        $shipment = get_post_meta($order->id, 'flagship_shipping_raw', true);

        $shipment_id = self::get_accessible_shipment_id($shipment);

        if (!$shipment_id) {
            return;
        }

        $response = $flagship->client()->delete('/pickups/'.$shipment['pickup']['id']);

        if ($response->get_code() == 204) {
            unset($shipment['pickup']);
            update_post_meta($order->id, 'flagship_shipping_raw', $shipment);
        }
    }

    protected function get_accessible_shipment_id($shipment)
    {
        $flagship = Flagship_Application::get_instance();
        $shipment_id = sanitize_text_field($_POST['flagship_shipping_shipment_id']);

        if (empty($shipment) || empty($shipment_id) || $shipment_id != $shipment['shipment_id']) {
            $flagship->notification->add('warning', 'Unable to access shipment with SmartshipID ('.$shipment_id.')');

            return false;
        }

        return $shipment_id;
    }
}
