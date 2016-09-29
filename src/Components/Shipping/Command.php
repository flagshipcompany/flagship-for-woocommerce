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

        return $this->validate($response);
    }

    public function confirm(\FS\Components\Http\Client $client, Factory\FormattedRequestInterface $request)
    {
        $response = $client->post(
            '/ship/confirm',
            $request->getRequest()
        );

        return $this->validate($response);
    }

    public function pickup(\FS\Components\Http\Client $client, Factory\FormattedRequestInterface $request)
    {
        $response = $client->post(
            '/pickups',
            $request->getRequest()
        );

        return $this->validate($response);
    }

    protected function validate(\FS\Components\Http\Response $response)
    {
        if (!$response->isSuccessful()) {
            $this->getApplicationContext()
                ->getComponent('\\FS\\Components\\Notifier')
                ->error('FlagShip API Error: '.$this->getApplicationContext()->getComponent('\\FS\\Components\\Html')->ul($response->getError()));
        }

        return $response;
    }
}
