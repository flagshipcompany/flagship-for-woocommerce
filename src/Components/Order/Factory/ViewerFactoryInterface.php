<?php

namespace FS\Components\Order\Factory;

interface ViewerFactoryInterface
{
    public function getViewer(\FS\Components\Shop\OrderInterface $order);
}
