<?php

namespace FS\Configurations\WordPress\View;

use FS\Injection\I;

class Vue extends \FS\Components\AbstractComponent
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

        load_plugin_textdomain(FLAGSHIP_SHIPPING_TEXT_DOMAIN);

        include I::directory('PLUGIN').'templates/'.$template.'.php';
    }
}
