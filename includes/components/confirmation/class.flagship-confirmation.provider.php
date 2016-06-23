<?php

require_once __DIR__.'/class.flagship-confirmation.php';

class Flagship_Confirmation_Provider
{
    public function provide(Flagship_Application $flagship)
    {
        $flagship['confirmation'] = new Flagship_Confirmation($flagship);
    }
}
