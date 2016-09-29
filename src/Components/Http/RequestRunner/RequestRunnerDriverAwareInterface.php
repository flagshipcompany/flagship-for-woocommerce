<?php

namespace FS\Components\Http\RequestRunner;

interface RequestRunnerDriverAwareInterface
{
    public function setRequestRunnerDriver(RequestRunnerInterface $driver);

    public function getRequestRunnerDriver();
}
