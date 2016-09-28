<?php

namespace FS\Components;

abstract class AbstractComponent extends \FS\Context\Support\AbstractApplicationObjectSupport
{
    protected static $scope = 'singleton';

    public static function getScope()
    {
        return static::$scope;
    }

    public function debug($var)
    {
        $this->getApplicationContext()
            ->getComponent('\\FS\\Components\\Debugger')
            ->log($var);
    }

    public function isContextRequired()
    {
        return true;
    }
}
