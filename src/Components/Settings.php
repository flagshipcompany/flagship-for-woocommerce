<?php

namespace FS\Components;

class Settings extends \FS\Container
{
    protected static $scope = 'singleton';

    public static function getScope()
    {
        return self::$scope;
    }
}
