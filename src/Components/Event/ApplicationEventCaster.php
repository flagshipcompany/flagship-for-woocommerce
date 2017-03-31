<?php

namespace FS\Components\Event;

use FS\Components\AbstractComponent;
use FS\Components\Factory\ComponentPostConstructInterface;
use FS\Context\ApplicationListenerInterface as Listener;
use FS\Context\ApplicationEventInterface as Event;
use FS\Container;

class ApplicationEventCaster extends AbstractComponent implements ComponentPostConstructInterface
{
    protected $listeners;

    public function postConstruct()
    {
        $this->listeners = new Container();
    }

    /**
     * register event listener.
     *
     * @param Listener $listener
     */
    public function addApplicationListener(Listener $listener)
    {
        $this->listeners[$listener->getSupportedEvent()] = $listener;

        return $this;
    }

    /**
     * try to associate event and event listener.
     *
     * @param Event $event
     *
     * @return mixed
     */
    public function castEvent(Event $event)
    {
        $reflected = new \ReflectionObject($event);
        $eventName = $reflected->getName();

        if (isset($this->listeners[$eventName])) {
            return $this->invokeListener($this->listeners[$eventName], $event);
        }
    }

    /**
     * invoke event listener.
     *
     * @param Listener $listener
     * @param Event    $event
     *
     * @return mixed
     */
    protected function invokeListener(Listener $listener, Event $event)
    {
        return $listener
            ->setApplicationContext($this->getApplicationContext())
            ->onApplicationEvent($event, $this->getApplicationContext());
    }
}
