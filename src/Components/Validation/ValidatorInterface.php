<?php

namespace FS\Components\Validation;

interface ValidatorInterface
{
    public function validate($target, \FS\Components\Notifier $notifier);
}
