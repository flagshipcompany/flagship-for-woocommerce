<?php

namespace FS\Configurations\WordPress\View\Factory;

class Driver extends \FS\Components\AbstractComponent implements \FS\Components\View\Factory\FactoryInterface, \FS\Components\Factory\DriverInterface
{
    const RESOURCE_METABOX = 'metabox';
    const RESOURCE_OPTION_PACKAGE_BOX = 'option-package-box';
    const RESOURCE_OPTION_LOG = 'option-log';

    public function getView($resource, $context = array())
    {
        switch ($resource) {
            case self::RESOURCE_METABOX:
                $view = new \FS\Configurations\WordPress\View\BasicView();

                return $view->setPath('meta-boxes/order-flagship-shipping-actions');
                // no break
            case self::RESOURCE_OPTION_PACKAGE_BOX:
                $view = new \FS\Configurations\WordPress\View\BasicView();

                return $view->setPath('option/package-box');
                // no break
            case self::RESOURCE_OPTION_LOG:
                $view = new \FS\Configurations\WordPress\View\BasicView();

                return $view->setPath('option/log');
                // no break
        }

        throw new \Exception('Unable to retieve View '.$resource);
    }
}
