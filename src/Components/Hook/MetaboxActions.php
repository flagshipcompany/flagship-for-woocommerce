<?php

namespace FS\Components\Hook;

class MetaboxActions extends Engine implements Factory\HookRegisterAwareInterface
{
    protected $type = 'action';

    public function register()
    {
        if (!is_admin()) {
            return;
        }

        // add meta boxes (eg: side box)
        $this->add('add_meta_boxes', 'addMetaBoxAction');
        $this->add('woocommerce_process_shop_order_meta', 'metaBoxActionWrapper');
    }

    public function addMetaBoxAction()
    {
        $metaBox = $this->getApplicationContext()->getComponent('\\FS\\Components\\Order\\MetaBox');

        add_meta_box(
            'wc-flagship-shipping-box',
            __('FlagShip', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
            array($this, 'metaBoxDisplayWrapper'),
            'shop_order',
            'side',
            'high'
        );
    }

    public function metaBoxActionWrapper($post_id, $post)
    {
        $wcOrder = wc_get_order($post_id);
        $shoppingOrder = $this->getApplicationContext()->getComponent('\\FS\\Components\\Order\\ShoppingOrder');

        $shoppingOrder->setWcOrder($wcOrder);

        $metaBox = $this->getApplicationContext()->getComponent('\\FS\\Components\\Order\\MetaBox');
        $rp = $this->getApplicationContext()->getComponent('\\FS\\Components\\Web\\RequestParam');

        // config components for metabox action usage
        $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Notifier')
            ->scope('shop_order', array('id' => $shoppingOrder->getId()));

        // load instance shipping method used by this shopping order
        $service = $shoppingOrder->getShippingService();
        $shippingMethodInstanceId = $service['instance_id'] ? $service['instance_id'] : false;

        $options = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Options')
            ->sync($shippingMethodInstanceId);

        // set up token
        $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Http\\Client')
            ->setToken($options->get('token'));

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
        $wcOrder = wc_get_order($post->ID);

        $metaBox = $this->getApplicationContext()->getComponent('\\FS\\Components\\Order\\MetaBox');
        $shoppingOrder = $this->getApplicationContext()->getComponent('\\FS\\Components\\Order\\ShoppingOrder');

        $shoppingOrder->setWcOrder($wcOrder);

        $metaBox->display($shoppingOrder);
    }
}
