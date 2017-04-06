<?php

namespace FS\Components\Validation;

use FS\Context\ApplicationContext as Context;

interface ValidatorInterface
{
    public function validate($target, Context $context);
}
