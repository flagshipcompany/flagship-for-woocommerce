<?php

class WC_Meta_Box_Order_Flagship_Shipping_Actions
{
    public static function register()
    {
        add_meta_box(
            'wc-flagship-shipping-box',
            __('FlagShip', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
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
        $ctx = Flagship_Application::get_instance();
        $order = wc_get_order($post->ID);

        $ctx->load('Shipment');
        $ctx['shipment']->initialize($order);

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
        $ctx = Flagship_Application::get_instance();

        $order = wc_get_order($post_id);

        $action = sanitize_text_field($ctx['request']->request->get('flagship_shipping_shipment_action'));

        switch ($action) {
            case 'shipment-create':
                $ctx->load('Shipment');
                $ctx['shipment']->initialize($order)->confirm();
                break;
            case 'shipment-void':
                $ctx->load('Shipment');
                $ctx['shipment']->initialize($order)->cancel();
                break;
            case 'shipment-requote':
                $ctx->load('Shipment');
                $ctx['shipment']->initialize($order)->requote();
                break;
            case 'pickup-schedule':
                $ctx->load('Pickup');
                $ctx['pickup']->initialize($order)->schedule();
                break;
            case 'pickup-void':
                $ctx->load('Pickup');
                $ctx['pickup']->initialize($order)->cancel();
                break;
        }
    }
}
