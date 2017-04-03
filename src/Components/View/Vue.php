<?php

namespace FS\Components\View;

use FS\Injection\I;
use FS\Components\AbstractComponent;

class Vue extends AbstractComponent
{
    public function notice(array $payloads = array())
    {
        $this->render('notice', $payloads);
    }

    public function notification(array $payloads = array())
    {
        $this->render('notification', $payloads);
    }

    public function render($template, array $payloads = array())
    {
        foreach ($payloads as $key => $val) {
            $$key = $val;
        }

        $ctx = $this->ctx;

        load_plugin_textdomain(I::textDomain());

        include I::directory('PLUGIN').'templates/'.$template.'.php';
    }
}
