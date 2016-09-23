<?php

require_once __DIR__.'/class.flagship-pickup.php';

class Flagship_Pickup_Provider
{
    public function provide(FSApplicationContext $flagship)
    {
        $flagship['pickup'] = new Flagship_Pickup($flagship);

        $flagship->dependency(array(
            'Order',
            'Shipment',
        ));
    }
}
