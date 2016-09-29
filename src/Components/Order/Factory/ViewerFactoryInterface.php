<?php

namespace FS\Components\Order\Factory;

interface ViewerFactoryInterface
{
    public function getViewer(\FS\Components\Order\ShoppingOrder $order);
}
