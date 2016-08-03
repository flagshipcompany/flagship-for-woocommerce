<?php

require_once __DIR__.'/../class.flagship-component.php';
require_once __DIR__.'/class.flagship-api-response.php';

class Flagship_Client extends Flagship_Component
{
    protected $token = null;
    protected $api_entry_point;
    protected $timeout;

    public function set_token($token)
    {
        $this->token = $token;

        return $this;
    }

    public function set_entry_point($entry_point)
    {
        $this->api_entry_point = $entry_point;

        return $this;
    }

    public function set_timeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function has_token()
    {
        return (bool) $this->token;
    }

    public function request($uri, $data = array(), $method = 'GET', array $headers = array())
    {
        $url = $this->api_entry_point.$uri;

        $args = array();
        $args['method'] = $method;
        $args['body'] = $data;

        $args['headers'] = $this->make_headers($headers);
        $args['timeout'] = $this->timeout; // seconds

        try {
            $response = wp_remote_request(esc_url_raw($url), $args);

            if (is_wp_error($response)) {
                throw new Exception($response->get_error_message(), 500 | wp_remote_retrieve_response_code($response));
            }
        } catch (Exception $e) {
            return new Flagship_Api_Response(array(
                'errors' => array(array($e->getMessage())),
                'content' => array(),
            ), $e->getCode());
        }

        return new Flagship_Api_Response(json_decode(wp_remote_retrieve_body($response), true), wp_remote_retrieve_response_code($response));
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

    protected function make_headers(array $headers)
    {
        $headers['X-Smartship-Token'] = $this->token;

        return $headers;
    }
}
