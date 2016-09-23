<?php

namespace FS\Components\Shipping\Factory;

abstract class AbstractRequestFactory extends \FS\Components\AbstractComponent implements FormattedRequestInterface, PayloadAwareInterface
{
    protected $payload;
    protected static $scope = 'prototype';

    public function getRequest()
    {
        $request = new \FS\Components\Shipping\FormattedRequest();

        $this->makeRequest($request);

        return $request;
    }

    public function setPayload($payload)
    {
        $this->payload = $payload;

        return $this;
    }

    abstract public function makeRequest(FormattedRequestInterface $request);

    protected function makeRequestPart(
        \FS\Components\Shipping\RequestBuilder\RequestBuilderInterface $builder,
        $data
    ) {
        return $builder->build($data);
    }
}
