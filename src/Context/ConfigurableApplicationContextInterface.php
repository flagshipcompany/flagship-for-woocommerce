<?php

namespace FS\Context;

interface ConfigurableApplicationContextInterface extends ApplicationContextInterface
{
	public function addApplicationListener(ApplicationListenerInterface $listener);
}