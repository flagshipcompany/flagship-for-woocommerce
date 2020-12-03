<?php

namespace FS\Components;

use FS\Injection\I;

class Viewer extends AbstractComponent
{
    public function notice(array $payloads = [])
    {
        $this->render('notice', $payloads);
    }

    public function notification(array $payloads = [])
    {
        $this->render('notification', $payloads);
    }

    public function render($template, array $payloads = [])
    {
        foreach ($payloads as $key => $val) {
            $$key = $val;
        }

        $ctx = $this->ctx;

        load_plugin_textdomain(I::textDomain());

        include I::directory('PLUGIN').'templates/'.$template.'.php';
    }
}
