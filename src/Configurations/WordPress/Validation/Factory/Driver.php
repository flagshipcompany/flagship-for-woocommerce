<?php

namespace FS\Configurations\WordPress\Validation\Factory;

class Driver extends \FS\Components\AbstractComponent implements \FS\Components\Validation\Factory\FactoryInterface, \FS\Components\Factory\DriverInterface
{
    public function getValidator($resource, $context = array())
    {
        switch ($resource) {
            case 'AddressEssential':
                return new \FS\Configurations\WordPress\Validation\AddressEssentialValidator();
                // no break
            case 'Phone':
                return new \FS\Configurations\WordPress\Validation\PhoneValidator();
                // no break
            case 'Settings':
                return new \FS\Configurations\WordPress\Validation\SettingsValidator();
                // no break
            case 'Integrity':
                return new \FS\Configurations\WordPress\Validation\IntegrityValidator();
                // no break
        }
    }
}
