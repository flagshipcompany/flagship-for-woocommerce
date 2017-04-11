<?php

namespace FS\Injection\Injector;

use FS\Injection\InjectorInterface;

class Group implements InjectorInterface
{
    use CommonsTrait {
        resolve as resolveCommon;
    }

    public function resolve()
    {
        if (!$this->resolveCommon()) {
            return;
        }

        $cb = $this->callback;

        $cb();
    }
}
