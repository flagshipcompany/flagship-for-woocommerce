<?php

namespace FS\Components;

use FS\Injection\I;

class Debugger extends AbstractComponent
{
    public function log($var)
    {
        I::__($var);
    }
}
