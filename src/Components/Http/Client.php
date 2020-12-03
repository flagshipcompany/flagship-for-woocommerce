<?php

namespace FS\Components\Http;

use FS\Components\AbstractComponent;
use FS\Components\Factory\ComponentInitializingInterface;
use FS\Injection\Http\Client as HttpClient;

class Client extends AbstractComponent implements ComponentInitializingInterface
{
    protected $options = [];

    protected $headers = [];

    public function afterPropertiesSet()
    {
        $this->options['runner'] = new HttpClient();

        $this->setDefaultHeaders();
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

        $this->addHeader('X-Smartship-Token', $token);

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

    public function addHeader($name, $value)
    {
        $this->headers[$name] = $value;

        return $this;
    }

    protected function getHeaders($json = false)
    {
        $headers = $this->headers;

        if (!$json) {
            unset($headers['Content-Type']);
        }

        return $headers;
    }

    protected function setDefaultHeaders()
    {
        $headers = [];

        $headers['X-F4WC-Version'] = $this->getApplicationContext()->setting('FLAGSHIP_FOR_WOOCOMMERCE_VERSION');
        $headers['X-App-Name'] = 'woocommerce';
        $headers['X-Store-Name'] = get_site_url();
        $headers['Content-Type'] = 'application/json';

        $this->headers = $headers;
    }
}
