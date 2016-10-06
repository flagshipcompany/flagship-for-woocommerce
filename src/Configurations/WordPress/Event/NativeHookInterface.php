<?php

namespace FS\Configurations\WordPress\Event;

interface NativeHookInterface
{
    const TYPE_ACTION = 'action';
    const TYPE_FILTER = 'filter';

    public function publishNativeHook(\FS\Context\ConfigurableApplicationContextInterface $context);

    public function getNativeHookType();
}
