<?php

namespace FS\Components\Shipping\Object;

class Courier
{
    public static $couriers = array(
        'UPS' => 'ups',
        'DHL' => 'dhl',
        'FedEx' => 'fedex',
        'Purolator' => 'purolator',
        'Canpar' => 'canpar',
        'GLS' => 'gls',
        'Nationex' => 'nationex',
        'Canada Post' => 'canadapost',
    );
}
