<?php

namespace FS\Components\Shipping;

class FormattedRequest implements Factory\FormattedRequestInterface
{
    protected $request;

    public function setRequestPart($key, $data)
    {
        $this->request[$key] = $data;
    }

    public function getRequest()
    {
        return $this->request;
    }
}
