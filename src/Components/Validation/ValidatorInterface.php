<?php

namespace FS\Components\Validation;

use FS\Components\Notifier;

interface ValidatorInterface
{
    public function validate($target, Notifier $notifier);
}
