<?php

class Flagship_Component
{
    protected $ctx;

    public function __construct(FSApplicationContext $ctx)
    {
        $this->ctx = $ctx;

        $this->bootstrap();
    }

    public function bootstrap()
    {
    }

    protected function console($value)
    {
        $this->ctx->load('Console');
        $this->ctx['console']->log($value);
    }
}
