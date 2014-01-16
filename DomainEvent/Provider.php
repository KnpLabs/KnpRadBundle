<?php

namespace Knp\RadBundle\DomainEvent;

interface Provider
{
    /**
     * empties and returns the list of events raised internally
     *
     * @return array
     **/
    public function popEvents();
}
