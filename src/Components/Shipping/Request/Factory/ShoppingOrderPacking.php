<?php

namespace FS\Components\Shipping\Request\Factory;

use FS\Components\Shipping\Request\Builder\Factory\RequestBuilderFactory;
use FS\Components\Shipping\Request\FormattedRequestInterface;

class ShoppingOrderPacking extends AbstractRequestFactory
{
    public function makeRequest(FormattedRequestInterface $request, RequestBuilderFactory $factory)
    {
        $request->add(
            'items',
            $this->makeRequestPart(
                $factory->resolve('ProductItem', array(
                    'type' => 'order',
                )),
                $this->payload
            )
        );

        $request->add(
            'boxes',
            $this->makeRequestPart(
                $factory->resolve('PackageBox', array(
                    'type' => 'order',
                )),
                $this->payload
            )
        );

        $request->add(
            'units',
            'imperial'
        );

        return $request;
    }
}
