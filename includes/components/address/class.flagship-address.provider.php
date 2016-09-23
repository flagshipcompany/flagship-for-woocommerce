<?php

require_once __DIR__.'/class.flagship-address.php';

class Flagship_Address_Provider
{
    public function provide(FSApplicationContext $flagship)
    {
        $flagship['address'] = new Flagship_Address($flagship);
    }
}
