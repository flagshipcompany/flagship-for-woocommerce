<?php

require_once __DIR__.'/class.flagship-client.php';

class Flagship_Client_Provider
{
    public function provide(FSApplicationContext $ctx)
    {
        $ctx['client'] = new Flagship_Client($ctx);
        $ctx['client']
            ->set_token($ctx['options']->get('token'))
            ->set_entry_point($ctx['configs']->get('FLAGSHIP_SHIPPING_API_ENTRY_POINT'))
            ->set_timeout($ctx['configs']->get('FLAGSHIP_SHIPPING_API_TIMEOUT'));
    }
}
