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
}
