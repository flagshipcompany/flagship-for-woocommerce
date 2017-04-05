<?php

namespace FS\Components\Validation\Factory;

use FS\Components\AbstractComponent;
use FS\Context\Factory\FactoryInterface;

class ValidatorFactory extends AbstractComponent implements FactoryInterface
{
    public function resolve($resource, $option = [])
    {
        return $this->resolveValidator($resource, $option)->setApplicationContext($this->getApplicationContext());
    }

    protected function resolveValidator($resource, $context = array())
    {
        switch ($resource) {
            case 'AddressEssential':
                return new \FS\Components\Validation\AddressEssentialValidator();
                // no break
            case 'Phone':
                return new \FS\Components\Validation\PhoneValidator();
                // no break
            case 'Settings':
                return new \FS\Components\Validation\SettingsValidator();
                // no break
            case 'Integrity':
                return new \FS\Components\Validation\IntegrityValidator();
                // no break
        }
    }
}
