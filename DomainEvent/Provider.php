<?php

namespace Knp\RadBundle\DomainEvent;

interface Provider
{
    /**
     * Empties and returns the list of events raised internally
     *
     * @return array
     **/
    public function popEvents();

    /**
     * Raise a domain event
     *
     * @param string $eventName
     * @param array  $properties
     */
    public function raise($eventName, array $properties = array());
}
