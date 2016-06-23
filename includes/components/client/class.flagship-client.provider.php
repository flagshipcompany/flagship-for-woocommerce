<?php

require_once __DIR__.'/class.flagship-client.php';

class Flagship_Client_Provider
{
    public function provide(Flagship_Application $flagship)
    {
        $flagship['client'] = new Flagship_Client();
        $flagship['client']->set_token($flagship['options']->get('token'));
    }
}
