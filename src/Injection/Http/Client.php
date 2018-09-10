<?php

namespace FS\Injection\Http;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Client
{
    public function post($uri, $body, array $options = [])
    {
        $request = new Request('POST', $uri, $body);

        if (isset($options['headers'])) {
            $request->withHeaders($options['headers']);
        }

        return $this->send($request, $options);
    }

    public function put($uri, $body, array $options = [])
    {
        $request = new Request('PUT', $uri, $body);

        if (isset($options['headers'])) {
            $request->withHeaders($options['headers']);
        }

        return $this->send($request, $options);
    }

    public function delete($uri, array $options = [])
    {
        $request = new Request('DELETE', $uri);

        if (isset($options['headers'])) {
            $request->withHeaders($options['headers']);
        }

        return $this->send($request, $options);
    }

    public function get($uri, array $options = [])
    {
        if (isset($options['query'])) {
            $uri = add_query_arg($options['query'], $uri);
        }

        $request = new Request('GET', $uri);

        if (isset($options['headers'])) {
            $request->withHeaders($options['headers']);
        }

        return $this->send($request, $options);
    }

    public function send(Request $request, array $options = [])
    {
        $args = [
            'method' => $request->getMethod(),
            'body' => $request->getBody(),
        ];

        if ($headers = $request->getHeaders()) {
            $args['headers'] = $headers;
        }

        if (isset($options['timeout'])) {
            $args['timeout'] = $options['timeout'];
        }

        $response = \wp_remote_request(esc_url_raw($request->getUri()), $args);

        if (\is_wp_error($response)) {
            $remoteResponseCode = \wp_remote_retrieve_response_code($response);
            $remoteResponseCode = empty($remoteResponseCode) ? 502 : $remoteResponseCode;

            return new Response($remoteResponseCode, [], [
                'content' => [],
                'errors' => [$response->get_error_message()],
                'notices' => [],
            ]);
        }

        return new Response(
            \wp_remote_retrieve_response_code($response),
            \wp_remote_retrieve_headers($response)->getAll(),
            json_decode(\wp_remote_retrieve_body($response), true)
        );
    }
}
