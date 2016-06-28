<?php

require_once __DIR__.'/class.flagship-shipment.php';

class Flagship_Shipment_Provider
{
    public function provide(Flagship_Application $flagship)
    {
        $flagship['shipment'] = new Flagship_Shipment($flagship);

        $flagship->dependency(array(
            'Order',
        ));
    }
}
