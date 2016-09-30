<?php

namespace FS\Components\Http;

class Client extends \FS\Components\AbstractComponent implements RequestRunner\RequestRunnerAwareInterface
{
    protected $token = null;
    protected $apiEntryPoint;
    protected $timeout;
    protected $requestRunner;

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

    public function setRequestRunner(RequestRunner\RequestRunnerInterface $requestRunner)
    {
        $this->requestRunner = $requestRunner;

        return $this;
    }

    public function getRequestRunner()
    {
        return $this->requestRunner;
    }

    public function request($uri, $data = array(), $method = 'GET', array $headers = array())
    {
        $configs = array();

        $configs['url'] = $this->apiEntryPoint.$uri;
        $configs['method'] = $method;
        $configs['data'] = $data;
        $configs['headers'] = $this->makeHeaders($headers);
        $configs['timeout'] = $this->timeout;

        $response = $this->getRequestRunner()->run($configs);

        return $response;
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
        $settings = $this->getApplicationContext()->getComponent('\\FS\\Components\\Settings');

        $headers['X-Smartship-Token'] = $this->token;
        $headers['X-F4WC-Version'] = $settings['FLAGSHIP_FOR_WOOCOMMERCE_VERSION'];

        return $headers;
    }
}
