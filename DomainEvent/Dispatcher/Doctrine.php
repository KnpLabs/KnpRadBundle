<?php

namespace Knp\RadBundle\DomainEvent\Dispatcher;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Knp\RadBundle\DomainEvent\Provider;

/**
 * Uses doctrine postFlush event,
 * to loop over all entities that implement the "Provider" interface,
 * and dipatches all the events using a symfony event dispatcher
 * It's important to use postFlush to ensure everything is saved correctly (transaction commited)
 * before doing extra stuff (like sending emails f.e).
 **/
class Doctrine
{
    protected $dispatcher;
    protected $entities = [];

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function postPersist(LifecycleEventArgs $event)
    {
        $this->keepProvider($event);
    }

    public function postUpdate(LifecycleEventArgs $event)
    {
        $this->keepProvider($event);
    }

    public function postRemove(LifecycleEventArgs $event)
    {
        $this->keepProvider($event);
    }

    public function postFlush(PostFlushEventArgs $event)
    {
        foreach ($this->entities as $entity) {
            foreach ($entity->popEvents() as $event) {
                $event->setSubject($entity);
                $this->dispatcher->dispatch($event->getName(), $event);
            }
        }
    }

    private function keepProvider(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();

        if (!$entity instanceof Provider) {
            return;
        }

        $this->entities[] = $entity;
    }
}
