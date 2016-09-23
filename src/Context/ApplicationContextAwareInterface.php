<?php

namespace FS\Context;

interface ApplicationContextAwareInterface
{
    public function setApplicationContext(ApplicationContextInterface $ctx);
}
