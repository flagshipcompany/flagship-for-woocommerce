<?php

namespace FS\Components;

class Debugger extends \FS\Components\AbstractComponent
{
    public function log($var)
    {
        $home = exec('echo ~');
        $text = $var;
        if (!is_string($var) && !is_array($var)) {
            ob_start();
            var_dump($var);
            $text = strip_tags(ob_get_clean());
        }
        if (is_array($var)) {
            $text = json_encode($var, JSON_PRETTY_PRINT);
        }
        file_put_contents($home.'/Desktop/data', date('Y-m-d H:i:s')."\t".print_r($text, 1)."\n", FILE_APPEND | LOCK_EX);
    }
}
