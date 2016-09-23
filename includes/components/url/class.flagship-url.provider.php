<?php

require_once __DIR__.'/class.flagship-url.php';

class Flagship_Url_Provider
{
    public function provide(FSApplicationContext $ctx)
    {
        $ctx['url'] = new Flagship_Url($ctx);
    }
}
