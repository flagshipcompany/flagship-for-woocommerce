<?php

require_once __DIR__.'/../class.flagship-component.php';

class Flagship_View extends Flagship_Component
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
        $values = apply_filters(FLAGSHIP_NAME_PREFIX.strtolower(__FUNCTION__).'_filter', $payloads, $template);

        foreach ($values as $key => $val) {
            $$key = $val;
        }

        $ctx = $this->ctx;

        load_plugin_textdomain('flagship-shipping');

        include FLS__PLUGIN_DIR.'templates/'.$template.'.php';
    }
}
