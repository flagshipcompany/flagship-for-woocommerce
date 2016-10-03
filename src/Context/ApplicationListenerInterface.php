<?php

namespace FS\Context;

interface ApplicationListenerInterface
{
	public function onApplicationEvent($event);
}