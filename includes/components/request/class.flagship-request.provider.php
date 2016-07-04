<?php

require_once __DIR__.'/class.flagship-request.php';

class Flagship_Request_Provider
{
    public function provide(Flagship_Application $ctx)
    {
        $ctx['request'] = new Flagship_Request($ctx);
    }
}
