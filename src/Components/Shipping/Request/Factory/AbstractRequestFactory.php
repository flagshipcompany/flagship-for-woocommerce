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
    protected $extraInfo = [];

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

    public function getExtraReqInfo()
    {
        return $this->extraInfo;
    }

    public function addExtraReqInfo(array $extraInfoFromBuilder)
    {
        $this->extraInfo = array_merge($this->extraInfo, $extraInfoFromBuilder);
    }

    abstract public function makeRequest(FormattedRequestInterface $request, RequestBuilderFactory $factory);

    protected function makeRequestPart(BuilderInterface $builder, $data)
    {
        $requestPart = $builder->build($data);

        if (isset($requestPart['extra_info'])) {
            $this->addExtraReqInfo($requestPart['extra_info']);
            unset($requestPart['extra_info']);
        }

        return $requestPart;
    }
}
