<?php

namespace FS\Components\Shipping;

use FS\Components\Shipping\Request\FormattedRequestInterface;

class FormattedRequest implements FormattedRequestInterface
{
    protected $request;

    public function add($key, $data)
    {
        $this->request[$key] = $data;
    }

    public function getRequest()
    {
        return $this->request;
    }
}
