<?php

namespace FS\Components\View;

use FS\Components\AbstractComponent;
use FS\Components\Web\RequestParam;

class BasicView extends AbstractComponent implements ViewInterface
{
    protected $path;

    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    public function render(array $model, RequestParam $request = null)
    {
        $vue = $this->getApplicationContext()
            ->_('\\FS\\Components\\View\\Vue');

        $vue->render($this->path, $model);
    }
}
