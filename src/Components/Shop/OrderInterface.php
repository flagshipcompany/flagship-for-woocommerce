<?php

namespace FS\Components\Shop;

interface OrderInterface
{
    public function setNativeOrder($nativeOrder);

    public function getNativeOrder();
}
