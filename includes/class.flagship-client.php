<?php

class Flagship_Api_Response
{
    public $content;
    public $code;

    public function __construct($content, $code)
    {
        $this->content = $content;
        $this->code = $code;
    }

    public function is_success()
    {
        $code_val = intval($this->code);

        return $code_val >= 200 && $code_val < 300;
    }

    public function get_code()
    {
        return $this->code;
    }

    public function get_content()
    {
        return $this->content;
    }
}

class Flagship_Client
{
    protected $token = null;
    protected $api_entry_point = 'http://127.0.0.1:3002';
    protected $default_data_filter;

    public function __construct($token = null)
    {
        $this->default_data_filter = FLAGSHIP_NAME_PREFIX.'api_request_filter';
        $this->set_token($token);
    }

    public function set_token($token)
    {
        $this->token = $token;

        return $this;
    }

    public function has_token()
    {
        return !!$this->token;
    }

    public function request($uri, $data = array(), $method = 'GET', array $headers = array())
    {
        $data = apply_filters($this->default_data_filter, $data, $uri);

        $url = $this->api_entry_point.$uri;

        $args = array();
        $args['method'] = $method;
        $args['body'] = $data;

        $args['headers'] = $this->make_headers($headers);
        $args['timeout'] = 14; // seconds

        try {
            $response = wp_remote_request(esc_url_raw($url), $args);

            if (is_wp_error($response)) {
                throw new Exception($response->get_error_message(), 500 | wp_remote_retrieve_response_code($response));
            }
        } catch(Exception $e) {
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
