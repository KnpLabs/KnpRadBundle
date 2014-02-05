<?php

namespace Knp\RadBundle\DomainEvent\Dispatcher;

use Doctrine\Common\EventSubscriber;

class Queued implements EventSubscriber
{
    private $queuedEventNames;
    private $events = array();

    public function __construct(array $queuedEventNamess = array())
    {
        $this->queuedEventNames = $queuedEventNames;
    }

    public function __call($method, array $arguments)
    {
        if (!in_array(substr($method, 2), $this->queuedEventNames)) {
            throw new \BadMethodCallException;
        }

        // TODO, obviously use a real queue message producer
        file_put_contents(tempnam(sys_get_temp_dir(), 'queue'), serialize($event));
    }

    public function getSubscribedEvents()
    {
        return $this->delayedDomainEvents;
    }
}
