<?php

namespace FS\Injection\Http;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use FS\Injection\Http\Message\MessageTrait;

class Request
{
    use MessageTrait;

    protected $method = 'GET';
    protected $uri;

    public function __construct(string $method, string $uri, $body = null)
    {
        $this->withMethod($method);
        $this->withUri($uri);
        $this->withBody($body);
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function withMethod(string $method)
    {
        $this->method = $method;

        return $this;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function withUri(string $uri)
    {
        $this->uri = $uri;

        return $this;
    }
}
