<?php

require_once __DIR__.'/class.flagship-package.php';

class Flagship_Package_Provider
{
    public function provide(FSApplicationContext $flagship)
    {
        $flagship['package'] = new Flagship_Package($flagship);
    }
}
