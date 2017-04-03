<?php

namespace FS\Components\Validation\Factory;

use FS\Components\AbstractComponent;

class ValidatorFactory extends AbstractComponent implements FactoryInterface
{
    public function getValidator($resource, $context = array())
    {
        return $this->resolveValidator($resource, $context)->setApplicationContext($this->getApplicationContext());
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
