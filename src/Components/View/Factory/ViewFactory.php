<?php

namespace FS\Components\View\Factory;

use FS\Components\AbstractComponent;
use FS\Components\View\BasicView;
use FS\Context\Factory\FactoryInterface;

class ViewFactory extends AbstractComponent implements FactoryInterface
{
    const RESOURCE_METABOX = 'metabox';
    const RESOURCE_OPTION_PACKAGE_BOX = 'option-package-box';
    const RESOURCE_OPTION_LOG = 'option-log';

    public function resolve($resource, $context = array())
    {
        $view = $this->resolveView($resource, $context);

        if ($view) {
            return $view->setApplicationContext($this->getApplicationContext());
        }

        throw new \Exception('Unable to resolve view: '.$resource, 500);
    }

    protected function resolveView($resource, $context = array())
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
