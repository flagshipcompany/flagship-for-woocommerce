<?php

require_once __DIR__.'/class.flagship-order.php';

class Flagship_Order_Provider
{
    public function provide(FSApplicationContext $flagship)
    {
        $flagship['order'] = new Flagship_Order($flagship);
    }
}
