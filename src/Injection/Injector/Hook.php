<?php

namespace FS\Injection\Injector;

use FS\Injection\InjectorInterface;

class Hook implements InjectorInterface
{
    use CommonsTrait {
        withOptions as withOptionsCommon;
        resolve as resolveCommon;
    }

    const TYPE_ACTION = 1;
    const TYPE_FILTER = 2;

    protected $hook;
    protected $priority = 10;
    protected $acceptedArgs = 1;
    protected $type = 1;

    public function withOptions(array $options = [])
    {
        $this->withOptionsCommon($options);

        if (isset($options['priority'])) {
            $this->withPriority($options['priority']);
        }

        if (isset($options['acceptedArgs'])) {
            $this->withAcceptedArgs($options['acceptedArgs']);
        }

        return $this;
    }

    public function withHook($hook)
    {
        $this->hook = $hook;

        return $this;
    }

    public function withPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    public function withAcceptedArgs($acceptedArgs)
    {
        $this->acceptedArgs = $acceptedArgs;

        return $this;
    }

    public function withType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function resolve()
    {
        if (!$this->resolveCommon()) {
            return;
        }

        $cb = $this->callback;

        if ($this->type == self::TYPE_ACTION) {
            return \add_action($this->hook, $cb, $this->priority, $this->acceptedArgs);
        }

        \add_filter($this->hook, $cb, $this->priority, $this->acceptedArgs);
    }
}
