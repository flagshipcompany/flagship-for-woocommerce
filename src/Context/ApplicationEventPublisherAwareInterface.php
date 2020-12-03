<?php

namespace FS\Context;

interface ApplicationEventPublisherAwareInterface
{
	public function setApplicationEventPublisher(ApplicationEventPublisherInterface $publisher);
}