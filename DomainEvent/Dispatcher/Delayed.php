<?php

namespace Knp\RadBundle\DomainEvent\Dispatcher;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\EventManager;

class Delayed implements EventSubscriber
{
    private $eventManager;
    private $delayedEventNames;
    private $events = array();

    public function __construct(EventManager $eventManager, array $delayedEventNames = array())
    {
        $this->eventManager = $eventManager;
        $this->delayedEventNames = $delayedEventNames;
    }

    public function __call($method, array $arguments)
    {
        if (!in_array(substr($method, 2), $this->delayedEventNames)) {
            throw new \BadMethodCallException;
        }

        $this->events[] = $arguments[0];
    }

    public function getSubscribedEvents()
    {
        return $this->delayedEventNames;
    }

    public function dispatchDelayedDomainEvents()
    {
        foreach ($this->events as $event) {
            $this->eventManager->dispatchEvent('onDelayed'.$event->getName(), $event);
        }
    }
}
