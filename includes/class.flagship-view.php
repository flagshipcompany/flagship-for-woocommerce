<?php

class Flagship_View
{
    public static function notice(array $payloads = array())
    {
        self::render('notice', $payloads);
    }

    public static function notification(array $payloads = array())
    {
        self::render('notification', $payloads);
    }

    public static function render($template, array $payloads = array())
    {
        $values = apply_filters(FLAGSHIP_NAME_PREFIX.strtolower(__FUNCTION__).'_filter', $payloads, $template);

        foreach ($values as $key => $val) {
            $$key = $val;
        }

        load_plugin_textdomain('flagship-shipping');

        include FLS__PLUGIN_DIR.'templates/'.$template.'.php';
    }
}
