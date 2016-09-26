<?php

namespace FS\Components;

abstract class AbstractComponent extends \FS\Context\Support\AbstractApplicationObjectSupport
{
    protected static $scope = 'singleton';

    public function getScope()
    {
        return self::$scope;
    }

    public function debug($var)
    {
        $this->getApplicationContext()->getComponent('\\FS\\Components\\Debugger')->log($var);
    }

    public function isContextRequired()
    {
        return true;
    }
}
