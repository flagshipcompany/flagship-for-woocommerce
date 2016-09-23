<?php

namespace FS\Components\Shipping\RateProcessors;

interface RateProcessorInterface
{
    public function getProcessedRates($rates);
}
