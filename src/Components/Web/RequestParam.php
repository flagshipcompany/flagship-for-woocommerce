<?php

namespace FS\Components\Web;

use FS\Components\AbstractComponent;
use FS\Components\Factory\ComponentInitializingInterface;

class RequestParam extends AbstractComponent implements ComponentInitializingInterface
{
    public $request;
    public $query;
    public $server;
    public $cookies;
    public $files;

    protected $method;

    public function afterPropertiesSet()
    {
        $this->request = new Parameter($_POST);
        $this->query = new Parameter($_GET);
        $this->cookies = new Parameter($_COOKIE);
        $this->server = new Parameter($_SERVER);
        $this->files = new Parameter($_FILES);

        $this->method = strtoupper($this->server->get('REQUEST_METHOD', 'GET'));
    }

    public function getMethod()
    {
        return $this->method;
    }
}
