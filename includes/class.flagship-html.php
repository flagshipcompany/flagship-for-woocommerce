<?php

class Flagship_Html
{
    public static function anchor($name_or_href, $text = null, array $extras = array())
    {
        $flagship = Flagship_Application::get_instance();

        $extras = array_merge(array('text_domain' => $flagship->text_domain, 'escape' => false, 'target' => false), $extras);

        if (!$text) {
            $text = $href;
        }

        if ($extras['escape']) {
            $url = $flagship->url_for($name_or_href, true);
            $href = $url ? $url : esc_url($name_or_href);
            $text = esc_html__($text, $extras['text_domain']);
        } else {
            $url = $flagship->url_for($name_or_href);
            $href = $url ? $url : $name_or_href;
            $text = __($text, $extras['text_domain']);
        }

        return '<a '.($extras['target'] ? 'target="_blank" ' : '').'href="'.$href.'">'.$text.'</a>';
    }

    public static function anchor_e($name_or_href, $text = null, array $extras = array())
    {
        echo self::anchor($name_or_href, $text, $extras);
    }
}
