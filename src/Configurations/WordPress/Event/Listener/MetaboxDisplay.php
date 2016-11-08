<?php

namespace FS\Configurations\WordPress\Event\Listener;

class MetaboxDisplay extends \FS\Components\AbstractComponent implements \FS\Context\ApplicationListenerInterface, \FS\Configurations\WordPress\Event\NativeHookInterface
{
    public function getSupportedEvent()
    {
        return 'FS\\Configurations\\WordPress\\Event\\MetaboxDisplayEvent';
    }

    public function onApplicationEvent(
        \FS\Context\ApplicationEventInterface $event,
        \FS\Context\ConfigurableApplicationContextInterface $context
    ) {
        $order = $event->getInput('order');

        $metaBox = $context
            ->getComponent('\\FS\\Components\\Order\\MetaBox');
        $notifier = $context
            ->getComponent('\\FS\\Components\\Notifier')
            ->scope('shop_order', array('id' => $order->getId()));

        $metaBox->display($order);
    }

    public function publishNativeHook(\FS\Context\ConfigurableApplicationContextInterface $context)
    {
        \add_action('add_meta_boxes', function () use ($context) {
            $context->getComponent('\\FS\\Components\\Order\\MetaBox');

            \add_meta_box(
                'wc-flagship-shipping-box',
                __('FlagShip', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                function ($postId, $post) use ($context) {
                    $event = new \FS\Configurations\WordPress\Event\MetaboxDisplayEvent();

                    $order = $context->getComponent('\\FS\\Components\\Shop\\Factory\\ShopFactory')->getModel('order', array(
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
