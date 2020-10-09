<?php

if (!function_exists('console')) {
    function console($var)
    {
        $logPath = getenv('FLAGSHIP_LOG_PATH');

        if (!$logPath) {
            return;
        }

        $trace = debug_backtrace();
        $t = reset($trace);
        $text = $var;
        if (!is_string($var) && !is_array($var)) {
            ob_start();
            var_dump($var);
            $text = strip_tags(ob_get_clean());
        }
        if (is_array($var)) {
            $text = json_encode($var, JSON_PRETTY_PRINT);
        }
        file_put_contents($logPath.'/data', date('Y-m-d H:i:s')."\t".print_r($t['file'].':'.$t['line']."\n".$text, 1)."\n", FILE_APPEND | LOCK_EX);
    }
}

if (!function_exists('get_array_value')) {
    function get_array_value($array, $key, $default = null)
    {
        if (!is_array($array)) {
            throw new Exception("Argument 1 must be an array");
        }

        if (!isset($array[$key])) {
            return $default;
        }

        return $array[$key];
    }
}

if (!function_exists('fcs_get_edit_post_link')) {
    function fcs_get_edit_post_link($order_id)
    {
        $edit_link = get_post_type_object(get_post($order_id)->post_type)->_edit_link;
        $link = admin_url(sprintf( $edit_link . '&action=edit', $order_id));

        return $link;
    }
}