<?php

require_once __DIR__.'/class.flagship-confirmation.php';

class Flagship_Confirmation_Provider
{
    public function provide(Flagship_Application $ctx)
    {
        $ctx->dependency(array(
            'Package',
        ));

        $ctx['confirmation'] = new Flagship_Confirmation($ctx);
    }
}
