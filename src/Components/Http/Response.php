<?php

namespace FS\Components\Http;

class Response
{
    public $content;
    public $code;

    public function __construct($content, $code)
    {
        $this->content = $content;
        $this->code = $code;
    }

    public function isSuccessful()
    {
        $code = intval($this->code);

        return $code >= 200 && $code < 300;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getError()
    {
        if ($this->code == 403) {
            return $this->content;
        }

        if (isset($this->content['errors'])) {
            return $this->content['errors'];
        }

        return array();
    }

    public function getBody()
    {
        if (isset($this->content['content'])) {
            return $this->content['content'];
        }
    }
}
