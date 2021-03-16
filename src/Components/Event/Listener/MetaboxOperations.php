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
                'shipment-export' => 'exportShipment',
                'pickup-schedule' => 'schedulePickup',
                'pickup-void' => 'voidPickup',
            ])
            ->before(function ($context) use ($shipping) {
                // apply middlware function before invoke controller method
                $context->alert(Notifier::SCOPE_SHOP_ORDER, ['order' => $shipping->getOrder()]);

                // load instance shipping method used by this shopping order
                $service = $shipping->getService();

                $option = $context
                    ->option()
                    ->sync(isset($service['instance_id']) ? $service['instance_id'] : false);

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
        // run after the native function in woocommerce, with priority = 40, set to 100 to run at the end.
        \add_action('woocommerce_process_shop_order_meta', function ($postId, $post) use ($context) {
            $factory = $context->_('\\FS\\Components\\Shipping\\Factory\\ShippingFactory');

            $shipping = $factory->resolve('shipping', [
                'id' => $postId,
            ]);

            $option = $context->option('autocomplete_order');
            $shipment = $shipping->getShipment();
            $order = $shipping->getOrder();

            if ($context->option('autocomplete_order') == 'yes'
                && $order->native()->get_status() == 'processing'
                && !$order->hasAttribute('flagship_shipping_requote_rates')
                && $shipment->isCreated()
            ) {
                $order->native()->update_status('completed');
            }
        }, 100, 2);

        return $this;
    }

    public function getNativeHookType()
    {
        return self::TYPE_ACTION;
    }
}
