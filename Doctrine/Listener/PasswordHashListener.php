<?php

namespace Knp\RadBundle\Doctrine\Listener;

use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

use Knp\RadBundle\Security\UserInterface;
use Knp\RadBundle\Security\RecoverableUserInterface;

class PasswordHashListener implements EventSubscriber
{
    private $encoderFactory;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    public function getSubscribedEvents()
    {
        return array(
            Events::prePersist,
            Events::preUpdate,
        );
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof UserInterface) {
            return;
        }
        if (null === $entity->getPlainPassword()) {
            return;
        }

        $this->updatePasswordHash($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof UserInterface) {
            return;
        }
        if (null === $entity->getPlainPassword()) {
            return;
        }

        $this->updatePasswordHash($entity);

        $args->setNewValue('password', $entity->getPassword());
    }

    private function updatePasswordHash(UserInterface $entity)
    {
        $password = $this->encoderFactory->getEncoder($entity)->encodePassword(
            $entity->getPlainPassword(), $entity->getSalt()
        );
        $entity->setPassword($password);
        $entity->eraseCredentials();

        if ($entity instanceof RecoverableUserInterface) {
            $entity->erasePasswordRecoveryKey();
        }
    }
}
