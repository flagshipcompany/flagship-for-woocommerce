<?php

require_once __DIR__.'/class.flagship-options.php';

class Flagship_Options_Provider
{
    public function provide(FSApplicationContext $ctx)
    {
        $ctx['options'] = new Flagship_Options($ctx);
    }
}
