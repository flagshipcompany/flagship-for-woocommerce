<?php

namespace FS\Injection\Http;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use FS\Injection\Http\Message\MessageTrait;

class Response
{
    use MessageTrait;

    protected $statusCode;

    public function __construct(int $statusCode, array $headers, $body = null)
    {
        $this->withBody($body);
        $this->withHeaders($headers);
        $this->withStatusCode($statusCode);
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function withStatusCode(int $statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function hasBody()
    {
        return $this->statusCode != 204 && $this->body;
    }

    public function hasContent()
    {
        return isset($this->body['content']) && $this->body['content'];
    }

    public function getContent()
    {
        if (!$this->hasContent()) {
            return;
        }

        return $this->body['content'];
    }

    public function hasErrors()
    {
        return isset($this->body['errors']) && $this->body['errors'];
    }

    public function getErrors()
    {
        if (!$this->hasErrors()) {
            return;
        }

        return $this->body['errors'];
    }

    public function hasNotices()
    {
        return isset($this->body['notices']) && $this->body['notices'];
    }

    public function getNotices()
    {
        if (!$this->hasNotices()) {
            return;
        }

        return $this->body['notices'];
    }

    public function isSuccessful()
    {
        return $this->getStatusCode() < 400;
    }
}
