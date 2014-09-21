<?php

namespace Knp\RadBundle\DomainEvent;

trait ProviderTrait
{
    private $events = [];

    public function popEvents()
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }

    public function raise($eventName, array $properties = array())
    {
        $this->events[] = new Event($eventName, $properties);
    }
}
