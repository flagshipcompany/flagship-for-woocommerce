<?php

namespace FS\Components\Validation\Factory;

use FS\Context\Factory\AbstractFactory;

class ValidatorFactory extends AbstractFactory
{
    public function resolveWithoutContext($resource, array $option = [])
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
