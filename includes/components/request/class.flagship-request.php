<?php

require_once __DIR__.'/../class.flagship-component.php';
require_once __DIR__.'/class.flagship-parameter.php';

class Flagship_Request extends Flagship_Component
{
    public $request;
    public $query;
    public $server;
    public $cookies;
    public $files;

    protected $method;

    public function bootstrap()
    {
        $this->create_from_globals();
    }

    public function create_from_globals()
    {
        $this->request = new Flagship_Parameter($_POST);
        $this->query = new Flagship_Parameter($_GET);
        $this->cookies = new Flagship_Parameter($_COOKIE);
        $this->server = new Flagship_Parameter($_SERVER);
        $this->files = new Flagship_Parameter($_FILES);

        $this->method = strtoupper($this->server->get('REQUEST_METHOD', 'GET'));
    }

    public function get_method()
    {
        return $this->method;
    }
}
