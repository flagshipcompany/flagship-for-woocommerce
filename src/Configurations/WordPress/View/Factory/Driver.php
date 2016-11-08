<?php

namespace FS\Configurations\WordPress\View\Factory;

class Driver extends \FS\Components\AbstractComponent implements \FS\Components\View\Factory\FactoryInterface, \FS\Components\Factory\DriverInterface
{
    const RESOURCE_METABOX = 'metabox';

    public function getView($resource, $context = array())
    {
        switch ($resource) {
            case self::RESOURCE_METABOX:
                $view = new \FS\Configurations\WordPress\View\MetaboxView();

                return $view;
                // no break
        }

        throw new \Exception('Unable to retieve View '.$resource);
    }
}
