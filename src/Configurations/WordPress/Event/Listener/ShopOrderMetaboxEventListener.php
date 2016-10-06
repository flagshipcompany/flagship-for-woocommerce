<?php

namespace FS\Configurations\WordPress\Event\Listener;

class ShopOrderMetaboxEventListener extends \FS\Components\AbstractComponent implements \FS\Context\ApplicationListenerInterface
{
	public function getSupportedEvent()
	{
		return 'FS\\Configurations\\WordPress\\Event\\ShopOrderMetaboxEvent';
	}

	public function onApplicationEvent(\FS\Context\ApplicationEventInterface $event)
	{
		$metaBox = $this->getApplicationContext()->getComponent('\\FS\\Components\\Order\\MetaBox');
        $order = $event->getInputs()['order'];

        $notifier = $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Notifier')
            ->scope('shop_order', array('id' => $order->getId()));

        $metaBox->display($order);
	}
}