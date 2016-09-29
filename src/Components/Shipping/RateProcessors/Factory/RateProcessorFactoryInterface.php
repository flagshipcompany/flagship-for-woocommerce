<?php

namespace FS\Components\Shipping\RateProcessors\Factory;

interface RateProcessorFactoryInterface
{
    public function getRateProceessor($type = 'quote');
}
