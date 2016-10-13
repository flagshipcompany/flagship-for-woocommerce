<?php

namespace FS\Components\Shipping\RateProcessor;

interface RateProcessorInterface
{
    public function getProcessedRates($rates, $payload = array());
}
