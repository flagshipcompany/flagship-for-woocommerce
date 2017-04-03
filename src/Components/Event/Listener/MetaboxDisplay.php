<?php

namespace FS\Components\Event\Listener;

use FS\Components\AbstractComponent;
use FS\Context\ApplicationListenerInterface;
use FS\Components\Event\NativeHookInterface;
use FS\Context\ConfigurableApplicationContextInterface as Context;
use FS\Context\ApplicationEventInterface as Event;
use FS\Components\Event\ApplicationEvent;

class MetaboxDisplay extends AbstractComponent implements ApplicationListenerInterface, NativeHookInterface
{
    public function getSupportedEvent()
    {
        return ApplicationEvent::METABOX_DISPLAY;
    }

    public function onApplicationEvent(Event $event, Context $context)
    {
        $order = $event->getInput('order');

        $controller = $context
            ->_('\\FS\\Components\\Shipping\\Shipment\\MetaboxController');
        $notifier = $context
            ->_('\\FS\\Components\\Notifier')
            ->scope('shop_order', array('id' => $order->getId()));

        $controller->display($order);
    }

    public function publishNativeHook(Context $context)
    {
        \add_action('add_meta_boxes', function () use ($context) {
            \add_meta_box(
                'wc-flagship-shipping-box',
                __('FlagShip', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                function ($postId, $post) use ($context) {
                    $event = new ApplicationEvent(ApplicationEvent::METABOX_DISPLAY);

                    $order = $context->_('\\FS\\Components\\Shop\\Factory\\ShopFactory')->getModel('order', array(
                        'id' => $postId,
                    ));

                    $event->setInputs(array(
                        'order' => $order,
                    ));

                    $context->publishEvent($event);
                },
                'shop_order',
                'side',
                'high'
            );
        });

        return $this;
    }

    public function getNativeHookType()
    {
        return self::TYPE_ACTION;
    }
}
