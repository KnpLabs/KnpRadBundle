<?php

namespace Knp\RadBundle\DomainEvent\Dispatcher;

use Doctrine\Common\EventSubscriber;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;

class Delayed implements EventSubscriber
{
    private $container;
    private $delayedEventNames;
    private $events = array();

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        try {
            $this->delayedEventNames = $this->container->getParameter('knp_rad.domain_event.delayed_event_names');
        } catch (InvalidArgumentException $e) {
            $this->delayedEventNames = [];
        }
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
        return $this->prepareEventNames($this->delayedEventNames);
    }

    public function dispatchDelayedDomainEvents()
    {
        $eventManager = $this->container->get('doctrine.dbal.default_connection.event_manager');
        foreach ($this->events as $event) {
            $eventManager->dispatchEvent('onDelayed' . $event->getName(), $event);
        }
    }

    private function prepareEventNames(array $events)
    {
        return array_map(function ($element) {
            return sprintf('on%s', $element);
        }, $events);
    }
}
