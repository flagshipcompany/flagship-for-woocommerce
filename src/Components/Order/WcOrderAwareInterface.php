<?php

namespace FS\Components\Order;

interface WcOrderAwareInterface
{
    public function setWcOrder($order);

    public function getWcOrder();
}
