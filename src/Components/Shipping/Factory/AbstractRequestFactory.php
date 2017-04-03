<?php

namespace FS\Components\Shipping\Factory;

use FS\Components\AbstractComponent;
use FS\Components\Shipping\RequestBuilder\Factory\RequestBuilderFactory;
use FS\Components\Shipping\RequestBuilder\RequestBuilderInterface;

abstract class AbstractRequestFactory extends AbstractComponent implements FormattedRequestInterface, PayloadAwareInterface
{
    protected $payload;
    protected static $scope = 'prototype';

    public function getRequest()
    {
        $request = new \FS\Components\Shipping\FormattedRequest();
        $factory = $this->getApplicationContext()
            ->_('\\FS\\Components\\Shipping\\RequestBuilder\\Factory\\RequestBuilderFactory');

        $this->makeRequest($request, $factory);

        return $request;
    }

    public function setPayload($payload)
    {
        $this->payload = $payload;

        return $this;
    }

    abstract public function makeRequest(FormattedRequestInterface $request, RequestBuilderFactory $factory);

    protected function makeRequestPart(RequestBuilderInterface $builder, $data)
    {
        return $builder->build($data);
    }
}
