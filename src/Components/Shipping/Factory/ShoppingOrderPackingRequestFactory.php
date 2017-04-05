<?php

namespace FS\Components\Shipping\Factory;

use FS\Components\Shipping\RequestBuilder\Factory\RequestBuilderFactory;

class ShoppingOrderPackingRequestFactory extends AbstractRequestFactory implements FormattedRequestInterface
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
