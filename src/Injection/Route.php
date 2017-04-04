<?php

namespace FS\Injection;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Route
{
    protected $name;
    protected $uri;
    protected $cb;
    protected $matched = false;

    public function __construct($name = null)
    {
        if ($name) {
            $this->withName($name);
        }
    }

    public function withName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function withCallback(callable $cb)
    {
        $this->cb = $cb;

        return $this;
    }

    public function withUri($uri)
    {
        $this->uri = ltrim($uri, '/');

        return $this;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function getUriRegExp()
    {
        return '^('.$this->uri.')$';
    }

    public function getUriQueryMap()
    {
        return 'index.php?pagename=$matches[1]';
    }

    public function hit()
    {
        $this->matched = true;

        return $this;
    }

    public function isHitten()
    {
        return $this->matched;
    }
}
