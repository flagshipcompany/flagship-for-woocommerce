<?php

namespace FS\Context;

interface ApplicationListenerInterface
{
    public function getSupportedEvent();

    public function onApplicationEvent(ApplicationEventInterface $event, ConfigurableApplicationContextInterface $context);
}
