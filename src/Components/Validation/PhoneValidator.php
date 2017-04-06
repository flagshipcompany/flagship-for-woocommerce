<?php

namespace FS\Components\Validation;

use FS\Context\ApplicationContext as Context;

class PhoneValidator extends AbstractValidator
{
    public function validate($target, Context $context)
    {
        preg_match('/^\+?[1]?[-. ]?\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/', $target, $matches);

        if (!$matches) {
            $context->alert('\''.$target.'\''.__(' is not a valid phone number.', FLAGSHIP_SHIPPING_TEXT_DOMAIN), 'warning');
        }
    }
}
