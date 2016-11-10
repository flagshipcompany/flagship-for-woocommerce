<?php

namespace FS\Components\Shipping\Factory;

class ShoppingOrderPackingRequestFactory extends AbstractRequestFactory implements FormattedRequestInterface
{
    public function makeRequest(FormattedRequestInterface $request, \FS\Components\Shipping\RequestBuilder\Factory\RequestBuilderFactory $factory)
    {
        $request->setRequestPart(
            'items',
            $this->makeRequestPart(
                $factory->getBuilder('ProductItem', array(
                    'type' => 'order',
                )),
                $this->payload
            )
        );

        $request->setRequestPart(
            'boxes',
            $this->makeRequestPart(
                $factory->getBuilder('PackageBox', array(
                    'type' => 'order',
                )),
                $this->payload
            )
        );

        $request->setRequestPart(
            'units',
            'imperial'
        );

        return $request;
    }
}
