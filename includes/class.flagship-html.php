<?php

class Flagship_Html
{
    public static function anchor($name_or_href, $text = null, array $extras = array())
    {
        $flagship = Flagship_Application::get_instance();

        $extras = array_merge(array('text_domain' => $flagship->text_domain, 'escape' => true, 'target' => false), $extras);

        if (!$text) {
            $text = $href;
        }

        $url = $flagship->url_for($name_or_href, $extras['escape']);

        if ($extras['escape']) {
            $href = $url ? $url : esc_url($name_or_href);
            $text = esc_html__($text, $extras['text_domain']);
        } else {
            $href = $url ? $url : $name_or_href;
            $text = __($text, $extras['text_domain']);
        }

        return '<a '.($extras['target'] ? 'target="_blank" ' : '').'href="'.$href.'">'.$text.'</a>';
    }

    public static function anchor_e($name_or_href, $text = null, array $extras = array())
    {
        echo self::anchor($name_or_href, $text, $extras);
    }

    public static function image($uri, $title = null, $extras = array())
    {
        $attributes = '';

        foreach ($extras as $attribute => $value) {
            $attributes .= ' '.$attribute.'="'.$value.'"';
        }

        return '<img src="'.plugins_url('/assets/images/'.$uri, dirname(__FILE__)).'"'.($title ? ' title="'.$title.'"' : '').$attributes.'/>';
    }

    public static function image_e($uri, $title = null, $extras = array())
    {
        echo self::image($uri, $title, $extras);
    }

    public static function array2list($arr)
    {
        $output = '<ul>';

        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                $output .= '<li><span>'.$k.'</span>';
                $output .= self::array2list($v);
                $output .= '</li>';

                continue;
            }

            $output .= '<li>'.$v.'</li>';
        }

        $output .= '</ul>';

        return $output;
    }

    public static function array2list_e($arr)
    {
        echo self::array2list($arr);
    }
}
