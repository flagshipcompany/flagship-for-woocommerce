<?php

namespace FS\Components\Event\Listener;

use FS\Components\AbstractComponent;
use FS\Context\ApplicationListenerInterface;
use FS\Components\Event\NativeHookInterface;
use FS\Context\ConfigurableApplicationContextInterface as Context;
use FS\Context\ApplicationEventInterface as Event;
use FS\Components\Event\ApplicationEvent;

class MetaboxOperations extends AbstractComponent implements ApplicationListenerInterface, NativeHookInterface
{
    public function getSupportedEvent()
    {
        return ApplicationEvent::METABOX_OPERATIONS;
    }

    public function onApplicationEvent(Event $event, Context $context)
    {
        $mapping = [
            'shipment-create' => 'createShipment',
            'shipment-void' => 'voidShipment',
            'shipment-requote' => 'requoteShipment',
            'pickup-schedule' => 'schedulePickup',
            'pickup-void' => 'voidPickup',
        ];

        $rp = $context
            ->_('\\FS\\Components\\Web\\RequestParam');

        if (!isset($mapping[$rp->request->get('flagship_shipping_shipment_action')])) {
            return;
        }

        $order = $event->getInput('order');

        $context
            ->controller('\\FS\\Components\\Shipping\\Controller\\MetaboxController')
            ->before(function ($context) use ($order) {
                // apply middlware function before invoke controller method
                $context
                    ->_('\\FS\\Components\\Notifier')
                    ->scope('shop_order', ['id' => $order->getId()]);

                // load instance shipping method used by this shopping order
                $service = $order->getShippingService();

                $options = $context
                    ->_('\\FS\\Components\\Options')
                    ->sync($service['instance_id'] ? $service['instance_id'] : false);

                $context
                    ->_('\\FS\\Components\\Http\\Client')
                    ->setToken($options->get('token'));
            })
            ->dispatch($mapping[$rp->request->get('flagship_shipping_shipment_action')], [$order]);
    }

    public function publishNativeHook(Context $context)
    {
        \add_action('woocommerce_process_shop_order_meta', function ($postId, $post) use ($context) {
            $event = new ApplicationEvent(ApplicationEvent::METABOX_OPERATIONS);
            $order = $context->_('\\FS\\Components\\Shop\\Factory\\ShopFactory')->getModel('order', array(
                'id' => $postId,
            ));

            $event->setInputs(array('order' => $order));

            $context->publishEvent($event);
        }, 10, 2);

        return $this;
    }

    public function getNativeHookType()
    {
        return self::TYPE_ACTION;
    }
}
