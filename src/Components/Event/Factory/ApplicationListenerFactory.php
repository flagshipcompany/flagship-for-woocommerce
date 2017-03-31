<?php

namespace FS\Components\Event\Factory;

use FS\Components\AbstractComponent;

class ApplicationListenerFactory extends AbstractComponent
{
    public function addApplicationListeners(array $listeners = [])
    {
        foreach ($listeners as $listener) {
            $reflected = new \ReflectionObject($listener);

            if ($reflected->implementsInterface('\\FS\\Configurations\\WordPress\\Event\\NativeHookInterface')) {
                $listener->publishNativeHook($this->getApplicationContext());
            }

            $this->getApplicationContext()->addApplicationListener($listener);
        }
    }
}
