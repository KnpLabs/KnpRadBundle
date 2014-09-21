<?php

namespace spec\Knp\RadBundle\DomainEvent\Dispatcher;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\UnitOfWork;
use Knp\RadBundle\DomainEvent\Provider;
use Knp\RadBundle\DomainEvent\Event;
use Doctrine\ORM\Event\LifecycleEventArgs;

class DoctrineSpec extends ObjectBehavior
{
    function let(EventDispatcherInterface $dispatcher)
    {
        $this->beConstructedWith($dispatcher);
    }

    function it_dispatches_domain_events_after_doctrine_unit_of_work_has_been_flushed(
        PostFlushEventArgs $postFlushEvent,
        LifecycleEventArgs $postPersistEvent,
        LifecycleEventArgs $postRemoveEvent,
        Provider $provider,
        Event $entityCreated,
        Event $propertyUpdated,
        NonProvider $nonProvider,
        $dispatcher
    ) {
        $postPersistEvent->getEntity()->willReturn($provider);
        $postRemoveEvent->getEntity()->willReturn($nonProvider);

        $provider->popEvents()->willReturn([$entityCreated, $propertyUpdated]);
        $entityCreated->getName()->willReturn('EntityCreated');
        $propertyUpdated->getName()->willReturn('PropertyUpdated');

        $entityCreated->setSubject($provider)->shouldBeCalled();
        $propertyUpdated->setSubject($provider)->shouldBeCalled();
        $dispatcher->dispatch('EntityCreated', $entityCreated)->shouldBeCalled();
        $dispatcher->dispatch('PropertyUpdated', $propertyUpdated)->shouldBeCalled();

        $this->postPersist($postPersistEvent);
        $this->postRemove($postRemoveEvent);
        $this->postFlush($postFlushEvent);
    }
}

class NonProvider
{
}
