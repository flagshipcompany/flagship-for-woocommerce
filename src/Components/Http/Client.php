<?php

namespace FS\Components\Http;

class Client extends \FS\Components\AbstractComponent
{
    protected $token = null;
    protected $apiEntryPoint;
    protected $timeout;

    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    public function setEntryPoint($apiEntryPoint)
    {
        $this->apiEntryPoint = $apiEntryPoint;

        return $this;
    }

    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function hasToken()
    {
        return (bool) $this->token;
    }

    public function request($uri, $data = array(), $method = 'GET', array $headers = array())
    {
        $url = $this->apiEntryPoint.$uri;

        $args = array();
        $args['method'] = $method;
        $args['body'] = $data;

        $args['headers'] = $this->makeHeaders($headers);
        $args['timeout'] = $this->timeout; // seconds

        try {
            $response = wp_remote_request(esc_url_raw($url), $args);

            if (is_wp_error($response)) {
                throw new \Exception($response->get_error_message(), 500 | wp_remote_retrieve_response_code($response));
            }
        } catch (Exception $e) {
            return new Response(array(
                'errors' => array(array($e->getMessage())),
                'content' => array(),
            ), $e->getCode());
        }

        $ar = new Response(json_decode(wp_remote_retrieve_body($response), true), wp_remote_retrieve_response_code($response));

        if (!$ar->isSuccessful()) {
            $this->getApplicationContext()
                ->getComponent('\\FS\\Components\\Notifier')
                ->error('FlagShip API Error: '.$this->getApplicationContext()->getComponent('\\FS\\Components\\Html')->ul($ar->getError()));
        }

        return $ar;
    }

    public function get($uri, array $data = array())
    {
        return $this->request(add_query_arg($data, $uri), $data, 'GET');
    }

    public function post($uri, array $data = array())
    {
        $data = json_encode($data);

        return $this->request($uri, $data, 'POST', array(
            'Content-Type' => 'application/json; charset=utf-8',
        ));
    }

    public function put($uri, array $data = array())
    {
        $data = json_encode($data);

        return $this->request($uri, $data, 'PUT', array(
            'Content-Type' => 'application/json; charset=utf-8',
        ));
    }

    public function delete($uri)
    {
        return $this->request($uri, array(), 'DELETE');
    }

    protected function makeHeaders(array $headers)
    {
        $headers['X-Smartship-Token'] = $this->token;

        return $headers;
    }
}
