<?php

namespace FS\Components\Shipping;

class Command extends \FS\Components\AbstractComponent
{
    public function quote(\FS\Components\Http\Client $client, Factory\FormattedRequestInterface $request)
    {
        $response = $client->post(
            '/ship/rates',
            $request->getRequest()
        );

        return $response;
    }

    public function confirm(\FS\Components\Http\Client $client, Factory\FormattedRequestInterface $request)
    {
        $response = $client->post(
            '/ship/confirm',
            $request->getRequest()
        );

        return $response;
    }

    public function pickup(\FS\Components\Http\Client $client, Factory\FormattedRequestInterface $request)
    {
        $response = $client->post(
            '/pickups',
            $request->getRequest()
        );

        return $response;
    }
}
