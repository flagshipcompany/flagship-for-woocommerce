<?php

namespace FS\Components\Order\Factory;

class MetaBoxViewer extends \FS\Components\AbstractComponent implements ViewerInterface
{
    protected $template;
    protected $payload;

    public function render()
    {
        $this->getApplicationContext()->getComponent('\\FS\\Components\\Viewer')->render($this->template, $this->payload);
    }

    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    public function setPayload($payload)
    {
        $this->payload = $payload;

        return $this;
    }
}
