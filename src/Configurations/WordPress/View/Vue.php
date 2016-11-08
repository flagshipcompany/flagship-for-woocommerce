<?php

namespace FS\Configurations\WordPress\View;

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

        include FLAGSHIP_SHIPPING_PLUGIN_DIR.'templates/'.$template.'.php';
    }
}
