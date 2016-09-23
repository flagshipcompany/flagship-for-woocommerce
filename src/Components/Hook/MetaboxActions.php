<?php

namespace FS\Components\Hook;

require_once FLAGSHIP_SHIPPING_PLUGIN_DIR.'includes/admin/post-types/class.wc-admin-post-types-flagship-shipping-pickup.php';

class MetaboxActions extends Engine implements Factory\HookRegisterAwareInterface
{
    protected $type = 'action';

    public function register()
    {
        if (!is_admin()) {
            return;
        }

        // add meta boxes (eg: side box)
        $this->add('add_meta_boxes');
        $this->add('woocommerce_process_shop_order_meta');

        // add pickup custom post type
        $this->add('init', 'woocomerce_register_post_type_action');
    }

    public function add_meta_boxes_action()
    {
        $metaBox = $this->ctx->getComponent('\\FS\\Components\\Order\\MetaBox');

        add_meta_box(
            'wc-flagship-shipping-box',
            __('FlagShip', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
            array($this, 'metaBoxDisplayWrapper'),
            'shop_order',
            'side',
            'high'
        );
    }

    public function woocomerce_register_post_type_action()
    {
        \Wc_Admin_Post_Types_Flagship_Shipping_Pickup::register();
    }

    public function woocommerce_process_shop_order_meta_action($post_id, $post)
    {
        $order = wc_get_order($post_id);
        $shoppingOrder = $this->ctx->getComponent('\\FS\\Components\\Order\\ShoppingOrder');

        $shoppingOrder->setWcOrder($order);

        $metaBox = $this->ctx->getComponent('\\FS\\Components\\Order\\MetaBox');
        $rp = $this->ctx->getComponent('\\FS\\Components\\Web\\RequestParam');

        switch ($rp->request->get('flagship_shipping_shipment_action')) {
            case 'shipment-create':
                $metaBox->createShipment($shoppingOrder);
                break;
            case 'shipment-void':
                $metaBox->voidShipment($shoppingOrder);
                break;
            case 'shipment-requote':
                $metaBox->requoteShipment($shoppingOrder);
                break;
            case 'pickup-schedule':
                $metaBox->schedulePickup($shoppingOrder);
                break;
            case 'pickup-void':
                $metaBox->voidPickup($shoppingOrder);
                break;
        }
    }

    public function metaBoxDisplayWrapper($post)
    {
        $order = wc_get_order($post->ID);

        $metaBox = $this->ctx->getComponent('\\FS\\Components\\Order\\MetaBox');
        $shoppingOrder = $this->ctx->getComponent('\\FS\\Components\\Order\\ShoppingOrder');

        $shoppingOrder->setWcOrder($order);

        $metaBox->display($shoppingOrder);
    }
}
