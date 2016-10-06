<?php

namespace FS\Configurations\WordPress\Event;

class ApplicationListenerFactory extends \FS\Components\AbstractComponent
{
	public function addApplicationListeners(\FS\Context\ConfigurableApplicationContextInterface $context)
	{
		$context->addApplicationListener(new Listener\ShopOrderMetaboxEventListener());
	}
}