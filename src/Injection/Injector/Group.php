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
        $this->resolveCommon();

        $cb = $this->callback;

        $cb();
    }
}
