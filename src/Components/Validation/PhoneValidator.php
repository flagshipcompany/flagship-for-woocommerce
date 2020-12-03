<?php

namespace FS\Components\Validation;

use FS\Context\ApplicationContext as Context;

class PhoneValidator extends AbstractValidator
{
    public function validate($target, Context $context)
    {
        preg_match('/^\+?[1]?[-. ]?\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/', $target, $matches);

        if (!$matches) {
            $context->alert()->warning('"%s" is not a valid phone number.', [$target]);
        }
    }
}
