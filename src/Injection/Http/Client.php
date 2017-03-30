<?php

namespace FS\Injection\Http;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Client
{
    public function post(string $uri, $body, array $options = [])
    {
        $request = new Request('POST', $uri, $body);

        if (isset($options['headers'])) {
            $request->withHeaders($options['headers']);
        }

        return $this->send($request);
    }

    public function send(Request $request)
    {
        $args = [
            'method' => $request->getMethod(),
            'body' => $request->getBody(),
        ];

        if ($headers = $request->getHeaders()) {
            $args['headers'] = $header;
        }

        $response = \wp_remote_request(esc_url_raw($request->getUri()), $args);

        if (\is_wp_error($response)) {
            throw new \Exception($response->get_error_message(), 500 | \wp_remote_retrieve_response_code($response));
        }

        return new Response(
            \wp_remote_retrieve_response_code($response),
            \wp_remote_retrieve_headers($response)->getAll(),
            json_decode(\wp_remote_retrieve_body($response), true)
        );
    }
}
