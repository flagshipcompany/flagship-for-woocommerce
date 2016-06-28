<?php

require_once __DIR__.'/class.flagship-view.php';

class Flagship_View_Provider
{
    public function provide(Flagship_Application $ctx)
    {
        $ctx['view'] = new Flagship_View($ctx);
    }
}
