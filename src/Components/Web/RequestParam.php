<?php

namespace FS\Components\Web;

class RequestParam extends \FS\Components\AbstractComponent implements \FS\Components\Factory\ComponentInitializingInterface
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
