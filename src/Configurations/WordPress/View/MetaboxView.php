<?php

namespace FS\Configurations\WordPress\View;

class MetaboxView extends \FS\Components\AbstractComponent implements \FS\Components\View\ViewInterface
{
    const PATH = 'meta-boxes/order-flagship-shipping-actions';

    public function render(array $model, \FS\Components\Web\RequestParam $request = null)
    {
        $vue = $this->getApplicationContext()
            ->getComponent('\\FS\\Configurations\\WordPress\\View\\Vue');

        $vue->render(self::PATH, $model);
    }
}
