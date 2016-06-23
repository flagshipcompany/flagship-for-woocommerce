<?php

class Flagship_Component
{
    protected $flagship;

    public function __construct(Flagship_Application $flagship)
    {
        $this->flagship = $flagship;
        $this->bootstrap();
    }

    public function bootstrap()
    {
    }
}
