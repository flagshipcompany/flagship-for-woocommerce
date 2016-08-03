<?php

class Flagship_Api_Response
{
    public $content;
    public $code;

    public function __construct($content, $code)
    {
        $this->content = $content;
        $this->code = $code;
    }

    public function is_success()
    {
        $code_val = intval($this->code);

        return $code_val >= 200 && $code_val < 300;
    }

    public function get_code()
    {
        return $this->code;
    }

    public function get_content()
    {
        return $this->content;
    }

    public function get_error()
    {
        if ($this->code == 403) {
            return $this->content;
        }

        if (isset($this->content['errors'])) {
            return $this->content['errors'];
        }

        return array();
    }
}
