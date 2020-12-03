<?php

namespace FS\Components\View;

interface ViewInterface
{
    public function render(array $model, \FS\Components\Web\RequestParam $request);
}
