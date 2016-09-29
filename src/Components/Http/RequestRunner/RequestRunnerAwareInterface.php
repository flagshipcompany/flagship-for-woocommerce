<?php

namespace FS\Components\Http\RequestRunner;

interface RequestRunnerAwareInterface
{
    public function setRequestRunner(RequestRunnerInterface $requestRunner);

    public function getRequestRunner();
}
