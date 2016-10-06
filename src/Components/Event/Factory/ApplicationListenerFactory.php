<?php

namespace FS\Components\Event\Factory;

class ApplicationListenerFactory extends \FS\Components\AbstractComponent
{
	protected $driver;

	public function addApplicationListeners(\FS\Context\ConfigurableApplicationContextInterface $context)
	{
		$this->getApplicationListenerFactoryDriver()->addApplicationListeners($context);	
	}

	public function getApplicationListenerFactoryDriver()
	{
		return $this->driver;
	}

	public function setApplicationListenerFactoryDriver($driver)
	{
		$this->driver = $driver;

		return $this;
	}
}