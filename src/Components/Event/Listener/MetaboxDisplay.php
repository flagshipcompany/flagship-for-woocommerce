<?php

namespace FS\Components\Event\Listener;

use FS\Components\AbstractComponent;
use FS\Context\ApplicationListenerInterface;
use FS\Components\Event\NativeHookInterface;
use FS\Context\ConfigurableApplicationContextInterface as Context;
use FS\Context\ApplicationEventInterface as Event;
use FS\Components\Event\ApplicationEvent;
use FS\Components\Alert\Notifier;

class MetaboxDisplay extends AbstractComponent implements ApplicationListenerInterface, NativeHookInterface
{
    public function getSupportedEvent()
    {
        return ApplicationEvent::METABOX_DISPLAY;
    }

    public function onApplicationEvent(Event $event, Context $context)
    {
        $shipping = $event->getInput('shipping');

        $context
            ->controller('\\FS\\Components\\Shipping\\Controller\\MetaboxController', [
                'metabox-build' => 'display',
            ])
            ->before(function ($context) use ($shipping) {
                // apply middlware function before invoke controller method
                $context->alert(Notifier::SCOPE_SHOP_ORDER, ['order' => $shipping->getOrder()]);

                $service = $shipping->getService();
                $option = $context
                    ->option()
                    ->sync($service['instance_id'] ? $service['instance_id'] : false);
                $context->api($option);
            })
            ->after(function ($context) {
                // as we are in metabox,
                // we have to explicit "show" notification
                // why? wordpress will render shop order after it dealt with any POST request to shop order
                // any alerts added previously (treating POST data) will be shown here
                $context->alert()->view();
            })
            ->dispatch('metabox-build', [$shipping]);
    }

    public function publishNativeHook(Context $context)
    {
        \add_action('add_meta_boxes', function () use ($context) {
            \add_meta_box(
                'wc-flagship-shipping-box',
                __('FlagShip', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                function ($postId, $post) use ($context) {
                    $event = new ApplicationEvent(ApplicationEvent::METABOX_DISPLAY);
                    $factory = $context->_('\\FS\\Components\\Shipping\\Factory\\ShippingFactory');

                    $shipping = $factory->resolve('shipping', [
                        'id' => $postId,
                    ]);

                    $event->setInputs([
                        'shipping' => $shipping,
                    ]);

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
