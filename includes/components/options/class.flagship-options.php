<?php

class Flagship_Options
{
    protected $options = array();

    public function __construct(array $options = array())
    {
        $this->options = $options;
    }

    public function get($name, $default = null)
    {
        return isset($this->options[$name]) ? $this->options[$name] : $default;
    }

    public function equal($name, $value)
    {
        return $this->get($name) == $value;
    }

    public function not_equal($name, $value)
    {
        return $this->get($name) != $value;
    }
}
