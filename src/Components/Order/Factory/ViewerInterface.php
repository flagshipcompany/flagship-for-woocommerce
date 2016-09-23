<?php

namespace FS\Components\Order\Factory;

interface ViewerInterface
{
    public function render();

    public function setTemplate($template);

    public function setPayload($payload);
}
