<?php

namespace FS\Components\Http\RequestRunner\Drivers;

class WordPress implements \FS\Components\Http\RequestRunner\RequestRunnerInterface
{
    public function run($configs)
    {
        $args = array(
            'method' => $configs['method'],
            'body' => $configs['data'],
            'headers' => $configs['headers'],
            'timeout' => $configs['timeout'],
        );

        $response = \wp_remote_request(esc_url_raw($configs['url']), $args);

        if (\is_wp_error($response)) {
            throw new \Exception($response->get_error_message(), 500 | \wp_remote_retrieve_response_code($response));
        }

        return new \FS\Components\Http\Response(json_decode(\wp_remote_retrieve_body($response), true), \wp_remote_retrieve_response_code($response));
    }
}
