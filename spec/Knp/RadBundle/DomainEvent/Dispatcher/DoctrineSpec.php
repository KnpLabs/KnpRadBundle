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

class DoctrineSpec extends ObjectBehavior
{
    function let(EventDispatcherInterface $dispatcher)
    {
        $this->beConstructedWith($dispatcher);
    }

    function it_is_a_doctrine_event_subscriber()
    {
        $this->shouldBeAnInstanceOf('Doctrine\Common\EventSubscriber');
    }

    function it_subscribes_to_the_postFlush_event()
    {
        $this->getSubscribedEvents()->shouldReturn(array('postFlush'));
    }

    function it_dispatches_domain_events_after_doctrine_unit_of_work_has_been_flushed(
        PostFlushEventArgs $event,
        EntityManager $em,
        UnitOfWork $uow,
        Provider $entity,
        Event $event1,
        Event $event2,
        $dispatcher
    ) {
        $event->getEntityManager()->willReturn($em);
        $em->getUnitOfWork()->willReturn($uow);
        $uow->getIdentityMap()->willReturn([[
            $entity
        ]]);
        $entity->popEvents()->willReturn([$event1, $event2]);
        $event1->getName()->willReturn('EntityCreated');
        $event2->getName()->willReturn('PropertyUpdated');

        $event1->setSubject($entity)->shouldBeCalled();
        $event2->setSubject($entity)->shouldBeCalled();
        $dispatcher->dispatch('EntityCreated', $event1)->shouldBeCalled();
        $dispatcher->dispatch('PropertyUpdated', $event2)->shouldBeCalled();

        $this->postFlush($event);
    }
}
