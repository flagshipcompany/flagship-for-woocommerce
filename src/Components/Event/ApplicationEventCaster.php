<?php

namespace FS\Components\Event;

class ApplicationEventCaster extends \FS\Components\AbstractComponent implements \FS\Components\Factory\ComponentPostConstructInterface
{
    protected $listeners;

    public function postConstruct()
    {
        $this->listener = new \FS\Container();
    }

    public function addApplicationListener(\FS\Context\ApplicationListenerInterface $listener)
    {
        $this->listeners[$listener->getSupportedEvent()] = $listener;

        return $this;
    }

    public function castEvent(\FS\Context\ApplicationEventInterface $event)
    {
        $reflected = new \ReflectionObject($event);
        $eventName = $reflected->getName();

        foreach ($this->listeners as $listener) {
            if ($eventName == $listener->getSupportedEvent()) {
                return $this->invokeListener($listener, $event);
            }
        }
    }

    protected function invokeListener(\FS\Context\ApplicationListenerInterface $listener, \FS\Context\ApplicationEventInterface $event)
    {
        return $listener
            ->setApplicationContext($this->getApplicationContext())
            ->onApplicationEvent($event, $this->getApplicationContext());
    }
}
