<?php

namespace FS\Configurations\WordPress\Event\Listener;

class MetaboxOperations extends \FS\Components\AbstractComponent implements \FS\Context\ApplicationListenerInterface, \FS\Configurations\WordPress\Event\NativeHookInterface
{
    public function getSupportedEvent()
    {
        return 'FS\\Configurations\\WordPress\\Event\\MetaboxOperationsEvent';
    }

    public function onApplicationEvent(
        \FS\Context\ApplicationEventInterface $event,
        \FS\Context\ConfigurableApplicationContextInterface $context
    ) {
        $order = $event->getInput('order');

        $metaBox = $context
            ->getComponent('\\FS\\Components\\Order\\MetaBox');
        $rp = $context
            ->getComponent('\\FS\\Components\\Web\\RequestParam');
        $notifier = $context
            ->getComponent('\\FS\\Components\\Notifier')
            ->scope('shop_order', array('id' => $order->getId()));

        // load instance shipping method used by this shopping order
        $service = $order->getShippingService();

        $options = $context
            ->getComponent('\\FS\\Components\\Options')
            ->sync($service['instance_id'] ? $service['instance_id'] : false);
        $context
            ->getComponent('\\FS\\Components\\Http\\Client')
            ->setToken($options->get('token'));

        switch ($rp->request->get('flagship_shipping_shipment_action')) {
            case 'shipment-create':
                $metaBox->createShipment($order);
                break;
            case 'shipment-void':
                $metaBox->voidShipment($order);
                break;
            case 'shipment-requote':
                $metaBox->requoteShipment($order);
                break;
            case 'pickup-schedule':
                $metaBox->schedulePickup($order);
                break;
            case 'pickup-void':
                $metaBox->voidPickup($order);
                break;
        }
    }

    public function publishNativeHook(\FS\Context\ConfigurableApplicationContextInterface $context)
    {
        \add_action('woocommerce_process_shop_order_meta', function ($postId, $post) use ($context) {
            $event = new \FS\Configurations\WordPress\Event\MetaboxOperationsEvent();
            $order = $context->getComponent('\\FS\\Components\\Shop\\Factory\\ShopFactory')->getOrder('order', array(
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
