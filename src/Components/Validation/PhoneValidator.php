<?php

namespace FS\Components\Validation;

use FS\Components\Notifier;

class PhoneValidator extends AbstractValidator implements ValidatorInterface
{
    public function validate($target, Notifier $notifier)
    {
        preg_match('/^\+?[1]?[-. ]?\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/', $target, $matches);

        if (!$matches) {
            $notifier->warning('\''.$target.'\''.__(' is not a valid phone number.', FLAGSHIP_SHIPPING_TEXT_DOMAIN));
        }
    }
}
