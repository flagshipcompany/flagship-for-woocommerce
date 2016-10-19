<?php

namespace FS\Configurations\WordPress\Validation;

class PhoneValidator extends \FS\Components\Validation\AbstractValidator implements \FS\Components\Validation\ValidatorInterface
{
    public function validate($target, \FS\Components\Notifier $notifier)
    {
        preg_match('/^\+?[1]?[-. ]?\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/', $target, $matches);

        if (!$matches) {
            $notifier->warning('\''.$target.'\''.__(' is not a valid phone number.', FLAGSHIP_SHIPPING_TEXT_DOMAIN));
        }
    }
}
