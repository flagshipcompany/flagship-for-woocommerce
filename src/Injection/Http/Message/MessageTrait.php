<?php

namespace FS\Injection\Http\Message;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

trait MessageTrait
{
    protected $headers = [];
    protected $body;

    public function getBody()
    {
        return $this->body;
    }

    public function withBody($body)
    {
        $this->body = $body;

        return $this;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getHeader($header)
    {
        if (!$this->hasHeader($header)) {
            return null;
        }

        $normalized = strtoupper($header);

        return $this->headers[$normalized];
    }

    public function withHeaders(array $headers)
    {
        $this->headers = $headers;

        return $this;
    }

    public function withHeader($header, $value)
    {
        $normalized = strtoupper($header);
        $this->headers[$normalized] = trim($value);

        return $this;
    }

    public function hasHeader($header)
    {
        $normalized = strtoupper($header);

        return isset($this->headers[$normalized]);
    }

    public function getHeaderLines()
    {
        $lines = [];

        foreach ($this->headers as $header => $value) {
            $lines[] = $header.':'.$value;
        }

        return $lines;
    }
}
