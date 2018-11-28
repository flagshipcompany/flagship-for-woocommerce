<?php

namespace FS\Components\Shipping;

use FS\Components\AbstractComponent;
use FS\Components\Http\Client;
use FS\Components\Shipping\Request\FormattedRequestInterface as FormattedRequest;
use FS\Injection\Http\Response;

class Command extends AbstractComponent
{
    public function quote(Client $client, FormattedRequest $request)
    {
        $response = $client->post(
            '/ship/rates',
            $request->getRequest()
        );

        return $this->validate($response);
    }

    public function prepare(Client $client, FormattedRequest $request)
    {
        $response = $client->post(
            '/ship/prepare',
            $request->getRequest()
        );

        return $this->validate($response);
    }
    
    public function confirm(Client $client, FormattedRequest $request)
    {
        $response = $client->post(
            '/ship/confirm',
            $request->getRequest()
        );

        return $this->validate($response);
    }

    public function pickup(Client $client, FormattedRequest $request)
    {
        $response = $client->post(
            '/pickups',
            $request->getRequest()
        );

        return $this->validate($response);
    }

    public function pack(Client $client, FormattedRequest $request)
    {
        $response = $client->post(
            '/ship/packing',
            $request->getRequest()
        );

        return $this->validate($response);
    }

    protected function validate(Response $response)
    {
        if (!$response->isSuccessful()) {
            $context = $this->getApplicationContext();

            $context->alert()->error('FlagShip API Error: '.$context->_('\\FS\\Components\\Html')->ul($response->getErrors()));
        }

        return $response;
    }
}
