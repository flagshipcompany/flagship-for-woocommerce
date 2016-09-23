<?php

require_once __DIR__.'/class.flagship-validation.php';

class Flagship_Validation_Provider
{
    public function provide(FSApplicationContext $flagship)
    {
        $flagship['validation'] = new Flagship_Validation($flagship['client']);
    }
}
