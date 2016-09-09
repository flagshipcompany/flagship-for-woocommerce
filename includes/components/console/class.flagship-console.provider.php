<?php

require_once __DIR__.'/class.flagship-console.php';

class Flagship_Console_Provider
{
    public function provide(Flagship_Application $ctx)
    {
        $ctx['console'] = new Flagship_Console($ctx);
    }
}
