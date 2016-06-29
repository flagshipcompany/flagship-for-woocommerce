<?php

class WC_Meta_Box_Order_Flagship_Shipping_Actions
{
    public static function register()
    {
        add_meta_box(
            'wc-flagship-shipping-box',
            __('FlagShip', 'flagship-shipping'),
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

        $ctx = Flagship_Application::get_instance();

        $ctx->load('Shipment');

        if (!is_int($thepostid)) {
            $thepostid = $post->ID;
        }

        if (!is_object($theorder)) {
            $theorder = wc_get_order($thepostid);
        }

        $ctx['shipment']->initialize($theorder);

        $shipment = $ctx['order']->get_meta('flagship_shipping_raw');

        $view_data = $ctx['shipment']->get_view_data();

        $ctx['notification']->scope('shop_order', array('id' => $ctx['order']->get_id()));
        $ctx['notification']->view();

        $ctx['view']->render('meta-boxes/order-flagship-shipping-actions', $view_data);
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
        $ctx = Flagship_Application::get_instance();

        $ctx->load('Shipment');
        $ctx['shipment']->initialize($order);

        $ctx['shipment']->confirm();
    }

    protected static function shipment_requote($order)
    {
        $ctx = Flagship_Application::get_instance();

        $ctx->load('Shipment');
        $ctx['shipment']->initialize($order);

        $ctx['shipment']->requote();
    }

    protected static function shipment_void($order)
    {
        $ctx = Flagship_Application::get_instance();

        $ctx->load('Shipment');
        $ctx['shipment']->initialize($order);

        $ctx['shipment']->cancel();
    }

    protected static function pickup_schedule($order)
    {
        $ctx = Flagship_Application::get_instance();

        $ctx->load('Pickup');

        $ctx['pickup']->initialize($order);
        $ctx['pickup']->schedule();
    }

    protected static function pickup_void($order)
    {
        $ctx = Flagship_Application::get_instance();

        $ctx->load('Pickup');

        $ctx['pickup']->initialize($order);
        $ctx['pickup']->cancel();
    }
}
