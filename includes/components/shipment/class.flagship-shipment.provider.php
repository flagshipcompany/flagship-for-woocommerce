<?php

require_once __DIR__.'/class.flagship-shipment.php';

class Flagship_Shipment_Provider
{
    public function provide(FSApplicationContext $ctx)
    {
        $ctx->dependency(array(
            'Order',
        ));

        $ctx['shipment'] = new Flagship_Shipment($ctx);
    }
}
