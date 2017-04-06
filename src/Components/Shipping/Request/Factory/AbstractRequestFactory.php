<?php

namespace FS\Components\Shipping\Request\Factory;

use FS\Components\AbstractComponent;
use FS\Components\Shipping\Request\Builder\Factory\RequestBuilderFactory;
use FS\Components\Shipping\Request\Builder\BuilderInterface;
use FS\Components\Shipping\Request\FormattedRequestInterface;

abstract class AbstractRequestFactory extends AbstractComponent implements FormattedRequestInterface, PayloadAwareInterface
{
    protected $payload;
    protected static $scope = 'prototype';

    public function getRequest()
    {
        $request = new \FS\Components\Shipping\FormattedRequest();
        $factory = $this->getApplicationContext()
            ->_('\\FS\\Components\\Shipping\\Request\\Builder\\Factory\\RequestBuilderFactory');

        $this->makeRequest($request, $factory);

        return $request;
    }

    public function setPayload($payload)
    {
        $this->payload = $payload;

        return $this;
    }

    abstract public function makeRequest(FormattedRequestInterface $request, RequestBuilderFactory $factory);

    protected function makeRequestPart(BuilderInterface $builder, $data)
    {
        return $builder->build($data);
    }
}
