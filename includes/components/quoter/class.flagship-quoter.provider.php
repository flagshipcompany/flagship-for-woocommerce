<?php

require_once __DIR__.'/class.flagship-quoter.php';

class Flagship_Quoter_Provider
{
    public function provide(Flagship_Application $flagship)
    {
        $flagship->dependency(array(
            'Package',
        ));

        $flagship['quoter'] = new Flagship_Quoter($flagship);
    }
}
