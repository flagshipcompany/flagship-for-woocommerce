<?php

require_once __DIR__.'/class.flagship-url.php';

class Flagship_Url_Provider
{
    public function provide(Flagship_Application $ctx)
    {
        $flagship['url'] = new Flagship_Url($ctx);
    }
}
