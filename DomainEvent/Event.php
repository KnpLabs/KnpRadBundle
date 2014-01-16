<?php

namespace Knp\RadBundle\DomainEvent;

use Doctrine\Common\EventArgs;

class Event extends EventArgs
{
    private $eventName;
    private $properties;
    private $subject;

    public function __construct($eventName, array $properties = array())
    {
        $this->eventName = $eventName;
        $this->properties = $properties;
    }

    public function getName()
    {
        return $this->eventName;
    }

    public function __get($name)
    {
        if (!array_key_exists($name, $this->properties)) {
            throw new \RuntimeException("Property '$name' does not exist on event '{$this->eventName}'");
        }

        return $this->properties[$name];
    }

    public function setSubject(Provider $subject)
    {
        $this->subject = $subject;
    }

    public function getSubject()
    {
        return $this->subject;
    }
}
