<?php

namespace FS\Configurations\WordPress\View;

class BasicView extends \FS\Components\AbstractComponent implements \FS\Components\View\ViewInterface
{
    protected $path;

    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    public function render(array $model, \FS\Components\Web\RequestParam $request = null)
    {
        $vue = $this->getApplicationContext()
            ->getComponent('\\FS\\Configurations\\WordPress\\View\\Vue');

        $vue->render($this->path, $model);
    }
}
