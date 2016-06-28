<?php

require_once __DIR__.'/class.flagship-html.php';

class Flagship_Html_Provider
{
    public function provide(Flagship_Application $ctx)
    {
        $ctx['html'] = new Flagship_Html($ctx);
    }
}
