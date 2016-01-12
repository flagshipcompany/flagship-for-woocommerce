<?php

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

    public function request($uri, array $data = array(), $method = 'GET', array $headers = array())
    {
        $data = apply_filters($this->default_data_filter, $data, $uri);

        $url = $this->api_entry_point.$uri;

        $args = array();
        $args['method'] = $method;
        $args['body'] = $data;

        if ($method == 'GET') {
            $url = add_query_arg($data, $url);
        }

        $args['headers'] = $headers;
        $args['headers']['X-Smartship-Token'] = $this->token;

        $response = wp_remote_request(esc_url_raw($url), $args);

        if (is_wp_error($response)) {
            throw new Exception($response->get_error_message(), 500 | wp_remote_retrieve_response_code($response));
        }

        return json_decode(wp_remote_retrieve_body($response), true);
    }

    public function get($uri, array $data = array())
    {
        return $this->request($uri, $data, 'GET');
    }
}
