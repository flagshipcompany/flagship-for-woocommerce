<?php

namespace FS\Components;

abstract class AbstractComponent implements \FS\Context\ApplicationContextAwareInterface
{
    protected $ctx;
    protected static $scope = 'singleton';

    public function getScope()
    {
        return self::$scope;
    }

    public function setApplicationContext(\FS\Context\ApplicationContextInterface $ctx)
    {
        $this->ctx = $ctx;

        return $this;
    }

    public function debug($var)
    {
        $this->ctx->getComponent('\\FS\\Components\\Debugger')->log($var);
    }
}
