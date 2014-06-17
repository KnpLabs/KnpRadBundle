<?php

namespace Knp\RadBundle\DomainEvent\Dispatcher;

use Doctrine\Common\EventSubscriber;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Delayed implements EventSubscriber
{
    private $container;
    private $delayedEventNames;
    private $events = array();

    public function __construct(ContainerInterface $container, array $delayedEventNames = array())
    {
        $this->container = $container;
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
        $eventManager = $this->container->get('doctrine.dbal.default_connection.event_manager');
        foreach ($this->events as $event) {
            $eventManager->dispatchEvent('onDelayed'.$event->getName(), $event);
        }
    }
}
