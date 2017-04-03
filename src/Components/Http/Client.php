<?php

namespace FS\Components\Http;

use FS\Components\AbstractComponent;
use FS\Components\Factory\ComponentInitializingInterface;
use FS\Injection\Http\Client as HttpClient;

class Client extends AbstractComponent implements ComponentInitializingInterface
{
    protected $options = [];

    public function afterPropertiesSet()
    {
        $this->options['runner'] = new HttpClient();
    }

    public function withOptions(array $options = [])
    {
        foreach ([
            'token',
            'timeout',
            'base',
            'runner',
        ] as $key) {
            if (isset($options[$key])) {
                $this->options[$key] = $options[$key];
            }
        }
    }

    public function setToken($token)
    {
        $this->options['token'] = $token;

        return $this;
    }

    public function get($uri, array $data = array())
    {
        $response = $this->options['runner']->get($this->options['base'].$uri, [
            'headers' => $this->getHeaders(),
            'timeout' => $this->options['timeout'],
            'query' => $data,
        ]);

        return $response;
    }

    public function post($uri, array $data = array())
    {
        // json data as a string
        $data = json_encode($data);

        $response = $this->options['runner']->post($this->options['base'].$uri, $data, [
            'headers' => $this->getHeaders(true),
            'timeout' => $this->options['timeout'],
        ]);

        return $response;
    }

    public function put($uri, array $data = array())
    {
        $data = json_encode($data);

        $response = $this->options['runner']->put($this->options['base'].$uri, $data, [
            'headers' => $this->getHeaders(true),
            'timeout' => $this->options['timeout'],
        ]);

        return $response;
    }

    public function delete($uri)
    {
        $response = $this->options['runner']->delete($this->options['base'].$uri, [
            'headers' => $this->getHeaders(),
            'timeout' => $this->options['timeout'],
        ]);

        return $response;
    }

    protected function getHeaders($json = false)
    {
        $headers = [];
        $settings = $this->getApplicationContext()->_('\\FS\\Components\\Settings');

        $headers['X-Smartship-Token'] = $this->options['token'];
        $headers['X-F4WC-Version'] = $settings['FLAGSHIP_FOR_WOOCOMMERCE_VERSION'];

        if ($json) {
            $headers['Content-Type'] = 'application/json; charset=utf-8';
        }

        return $headers;
    }
}
