<?php

namespace FS\Components;

use FS\Injection\I;

class Html extends \FS\Components\AbstractComponent
{
    public function a($name_or_href, $text = null, array $extras = array())
    {
        $extras = array_merge(array('text_domain' => I::textDomain(), 'escape' => true, 'target' => false), $extras);

        if (!$text) {
            $text = $href;
        }

        $url = $this->getApplicationContext()->getComponent('\\FS\\Components\\Url')->make($name_or_href, $extras['escape']);

        if ($extras['escape']) {
            $href = $url ? $url : esc_url($name_or_href);
            $text = esc_html__($text, $extras['text_domain']);
        } else {
            $href = $url ? $url : $name_or_href;
            $text = __($text, $extras['text_domain']);
        }

        return '<a '.($extras['target'] ? 'target="_blank" ' : '').'href="'.$href.'">'.$text.'</a>';
    }

    public function a_e($name_or_href, $text = null, array $extras = array())
    {
        echo $this->a($name_or_href, $text, $extras);
    }

    public function img($uri, $title = null, $extras = array())
    {
        $attributes = '';

        foreach ($extras as $attribute => $value) {
            $attributes .= ' '.$attribute.'="'.$value.'"';
        }

        return '<img src="'.plugins_url('/assets/images/'.$uri, dirname(__FILE__)).'"'.($title ? ' title="'.$title.'"' : '').$attributes.'/>';
    }

    public function img_e($uri, $title = null, $extras = array())
    {
        echo $this->image($uri, $title, $extras);
    }

    public function ul($arr)
    {
        if (!is_array($arr)) {
            return $arr;
        }

        $output = '<ul>';

        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                $output .= '<li><span>'.$k.'</span>';
                $output .= $this->ul($v);
                $output .= '</li>';

                continue;
            }

            $output .= '<li>'.$v.'</li>';
        }

        $output .= '</ul>';

        return $output;
    }

    public function ul_e($arr)
    {
        echo $this->ul($arr);
    }
}
