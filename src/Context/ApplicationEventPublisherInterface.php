<?php

namespace FS\Context;

interface ApplicationEventPublisherInterface
{
	public function publishEvent($event);
}