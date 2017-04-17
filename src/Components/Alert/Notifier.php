<?php

namespace FS\Components\Alert;

use FS\Components\Factory\ComponentInitializingInterface;
use FS\Components\AbstractComponent;

class Notifier extends AbstractComponent implements ComponentInitializingInterface
{
    const SCOPE_CART = 'cart';
    const SCOPE_SHOP_ORDER = 'shop_order';

    protected $prev = [];
    protected $viewer;
    protected $scenario = null;

    /**
     * load viewer after notifier is instantiated.
     */
    public function afterPropertiesSet()
    {
        $this->setViewer($this->getApplicationContext()->_('\\FS\\Components\\Viewer'));
    }

    public function add($type, $message)
    {
        $this->scenario->add($type, $message);

        return $this;
    }

    public function error($message)
    {
        return $this->add('error', $message);
    }

    public function notice($message)
    {
        return $this->add('notice', $message);
    }

    public function warning($message)
    {
        return $this->add('warning', $message);
    }

    public function view()
    {
        $this->scenario->view($this->getApplicationContext()->_('\\FS\\Components\\Viewer'));

        if ($this->prev) {
            return $this->restore();
        }

        $this->scenario();

        return $this;
    }

    public function scenario($scenario = 'native', $extras = [])
    {
        if ($this->scenario && !$this->scenario->isEmpty()) {
            $this->prev = [
                'scenario' => $this->scenario,
            ];
        }

        $context = $this->getApplicationContext();

        switch ($scenario) {
            case self::SCOPE_SHOP_ORDER:
                $this->scenario = $context->_('\\FS\\Components\\Alert\\Scenario\\Order')->withOption($extras);
                break;
            case self::SCOPE_CART:
                $this->scenario = $context->_('\\FS\\Components\\Alert\\Scenario\\Cart');
                break;
            default:
                $this->scenario = $context->_('\\FS\\Components\\Alert\\Scenario\\Native');
                break;
        }

        // $this->scenario = $scenario;
        $this->extras = $extras;

        return $this;
    }

    public function getScenario()
    {
        return $this->scenario;
    }

    public function setViewer(\FS\Components\Viewer $viewer)
    {
        $this->viewer = $viewer;

        return $this;
    }

    protected function restore()
    {
        $this->scenario = $this->prev['scenario'];
        $this->prev = [];

        return $this;
    }
}
