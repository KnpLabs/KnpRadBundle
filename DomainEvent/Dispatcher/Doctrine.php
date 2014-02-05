<?php

namespace Knp\RadBundle\DomainEvent\Dispatcher;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Knp\RadBundle\DomainEvent\Provider;

/**
 * Uses doctrine postFlush event,
 * to loop over all entities that implement the "Provider" interface,
 * and dipatches all the events using doctrine event manager.
 * It's important to use postFlush to ensure everything is saved correctly (transaction commited)
 * before doing extra stuff (like sending emails f.e).
 **/
class Doctrine implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return array(
            'postFlush',
        );
    }

    public function postFlush(PostFlushEventArgs $event)
    {
        $em = $event->getEntityManager();
        $evm = $em->getEventManager();
        $identityMap = $em->getUnitOfWork()->getIdentityMap();

        foreach ($identityMap as $class => $entities) {
            foreach ($entities as $entity) {
                if (!$entity instanceof Provider) {
                    continue;
                }

                foreach ($entity->popEvents() as $event) {
                    $event->setSubject($entity);
                    $evm->dispatchEvent('on'.$event->getName(), $event);
                }
            }
        }
    }
}
