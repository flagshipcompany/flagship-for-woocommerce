<?php

namespace FS\Components;

use FS\Injection\I;

class Viewer extends \FS\Components\AbstractComponent
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

        load_plugin_textdomain(I::get('TEXT_DOMAIN'));

        include I::directory('PLUGIN').'templates/'.$template.'.php';
    }
}
