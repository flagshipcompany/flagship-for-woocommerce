<?php

require_once __DIR__.'/class.flagship-configs.php';

class Flagship_Configs_Provider
{
    public function provide(Flagship_Application $ctx)
    {
        $ctx['configs'] = new Flagship_Configs($ctx);
    }
}
