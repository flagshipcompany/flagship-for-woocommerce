<?php

namespace FS\Components\View\Factory;

use FS\Context\Factory\AbstractFactory;
use FS\Components\View\BasicView;

class ViewFactory extends AbstractFactory
{
    const RESOURCE_METABOX = 'metabox';
    const RESOURCE_OPTION_PACKAGE_BOX = 'option-package-box';
    const RESOURCE_OPTION_LOG = 'option-log';

    public function resolveWithoutContext($resource, array $option = [])
    {
        switch ($resource) {
            case self::RESOURCE_METABOX:
                $view = new BasicView();

                return $view->setPath('meta-boxes/order-flagship-shipping-actions');
                // no break
            case self::RESOURCE_OPTION_PACKAGE_BOX:
                $view = new BasicView();

                return $view->setPath('option/package-box');
                // no break
            case self::RESOURCE_OPTION_LOG:
                $view = new BasicView();

                return $view->setPath('option/log');
                // no break
        }

        throw new \Exception('Unable to retieve View '.$resource);
    }
}
