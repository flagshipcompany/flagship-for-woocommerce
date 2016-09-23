<?php

require_once __DIR__.'/class.flagship-configs.php';

class Flagship_Configs_Provider
{
    public function provide(FSApplicationContext $ctx)
    {
        $ctx['configs'] = new Flagship_Configs($ctx);
    }
}
