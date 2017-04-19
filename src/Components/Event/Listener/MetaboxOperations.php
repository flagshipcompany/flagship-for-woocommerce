<?php

namespace FS\Components\Event\Listener;

use FS\Components\AbstractComponent;
use FS\Context\ApplicationListenerInterface;
use FS\Components\Event\NativeHookInterface;
use FS\Context\ConfigurableApplicationContextInterface as Context;
use FS\Context\ApplicationEventInterface as Event;
use FS\Components\Event\ApplicationEvent;
use FS\Components\Alert\Notifier;

class MetaboxOperations extends AbstractComponent implements ApplicationListenerInterface, NativeHookInterface
{
    public function getSupportedEvent()
    {
        return ApplicationEvent::METABOX_OPERATIONS;
    }

    public function onApplicationEvent(Event $event, Context $context)
    {
        $rp = $context
            ->_('\\FS\\Components\\Web\\RequestParam');
        $shipping = $event->getInput('shipping');

        $context
            ->controller('\\FS\\Components\\Shipping\\Controller\\MetaboxController', [
                'shipment-create' => 'createShipment',
                'shipment-void' => 'voidShipment',
                'shipment-requote' => 'requoteShipment',
                'pickup-schedule' => 'schedulePickup',
                'pickup-void' => 'voidPickup',
            ])
            ->before(function ($context) use ($shipping) {
                // apply middlware function before invoke controller method
                $context
                    ->_('\\FS\\Components\\Alert\\Notifier')
                    ->scenario(Notifier::SCOPE_SHOP_ORDER, ['order' => $shipping->getOrder()]);

                // load instance shipping method used by this shopping order
                $service = $shipping->getService();

                $option = $context
                    ->option()
                    ->sync($service['instance_id'] ? $service['instance_id'] : false);

                $context->api($option);
            })
            ->dispatch($rp->request->get('flagship_shipping_shipment_action'), [$shipping]);
    }

    public function publishNativeHook(Context $context)
    {
        \add_action('woocommerce_process_shop_order_meta', function ($postId, $post) use ($context) {
            $event = new ApplicationEvent(ApplicationEvent::METABOX_OPERATIONS);
            $factory = $context->_('\\FS\\Components\\Shipping\\Factory\\ShippingFactory');

            $shipping = $factory->resolve('shipping', [
                'id' => $postId,
            ]);

            $event->setInputs(['shipping' => $shipping]);

            $context->publishEvent($event);
        }, 10, 2);

        return $this;
    }

    public function getNativeHookType()
    {
        return self::TYPE_ACTION;
    }
}
