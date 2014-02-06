<?php

namespace spec\Knp\RadBundle\Doctrine\Listener;

use PhpSpec\ObjectBehavior;
use Doctrine\ORM\Events;

class PasswordHashListenerSpec extends ObjectBehavior
{
    /**
     * @param Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface  $encoderFactory
     * @param Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface $encoder
     */
    function let($encoderFactory, $encoder)
    {
        $encoderFactory->getEncoder(\Prophecy\Argument::any())->willReturn($encoder);
        $encoder->encodePassword(\Prophecy\Argument::cetera())->will(function($args) {
            return $args[0].'#'.$args[1];
        });

        $this->beConstructedWith($encoderFactory);
    }

    function it_should_be_doctrine2_event_subscriber()
    {
        $this->shouldBeAnInstanceOf('Doctrine\Common\EventSubscriber');
    }

    function it_should_support_persist_and_update_events()
    {
        $this->getSubscribedEvents()->shouldReturn(array(
            Events::prePersist,
            Events::preUpdate,
        ));
    }

    /**
     * @param Doctrine\ORM\Event\LifecycleEventArgs $args
     * @param Knp\RadBundle\Security\UserInterface  $entity
     */
    function its_prePersist_should_rehash_user_password_if_new_password_providen($args, $entity)
    {
        $args->getEntity()->willReturn($entity);

        $entity->getPlainPassword()->willReturn('custom_pass');
        $entity->getSalt()->willReturn('some_salt');
        $entity->setPassword('custom_pass#some_salt')->shouldBeCalled();
        $entity->eraseCredentials()->shouldBeCalled();

        $this->prePersist($args);
    }

    /**
     * @param Doctrine\ORM\Event\LifecycleEventArgs           $args
     * @param Knp\RadBundle\Security\RecoverableUserInterface $entity
     */
    function its_prePersist_should_erase_password_recovery_key_for_recoverable_user($args, $entity)
    {
        $args->getEntity()->willReturn($entity);

        $entity->getPlainPassword()->willReturn('custom_pass');
        $entity->getSalt()->willReturn('some_salt');

        $entity->erasePasswordRecoveryKey()->shouldBeCalled();
        $entity->setPassword('custom_pass#some_salt')->shouldBeCalled();
        $entity->eraseCredentials()->shouldBeCalled();

        $this->prePersist($args);
    }

    /**
     * @param Doctrine\ORM\Event\LifecycleEventArgs $args
     * @param Knp\RadBundle\Security\UserInterface  $entity
     */
    function its_prePersist_should_not_touch_entity_if_no_new_password_providen($args, $entity)
    {
        $args->getEntity()->willReturn($entity);

        $entity->getPlainPassword()->willReturn(null);
        $entity->getSalt()->willReturn('some_salt');
        $entity->setPassword(\Prophecy\Argument::any())->shouldNotBeCalled();

        $this->prePersist($args);
    }

    /**
     * @param Doctrine\ORM\Event\LifecycleEventArgs $args
     * @param stdClass                              $entity
     */
    function its_prePersist_should_not_touch_entities_without_interface($args, $entity)
    {
        $args->getEntity()->willReturn($entity);

        $this->prePersist($args);
    }

    /**
     * @param Doctrine\ORM\Event\PreUpdateEventArgs $args
     * @param Doctrine\ORM\EntityManagerInterface   $em
     * @param Doctrine\ORM\UnitOfWork               $uow
     * @param Knp\RadBundle\Security\UserInterface  $entity
     */
    function its_preUpdate_should_rehash_user_password_if_new_password_providen($args, $em, $uow, $entity)
    {
        $args->getEntity()->willReturn($entity);

        $entity->getPlainPassword()->willReturn('custom_pass');
        $entity->getSalt()->willReturn('some_salt');

        $entity->setPassword('custom_pass#some_salt')->shouldBeCalled();
        $entity->eraseCredentials()->shouldBeCalled();

        $entity->getPassword()->willReturn('custom_pass#some_salt');
        $args->getEntityManager()->willReturn($em);
        $em->getUnitOfWork()->willReturn($uow);
        $uow->computeChangeSets()->shouldBeCalled();

        $this->preUpdate($args);
    }

    /**
     * @param Doctrine\ORM\Event\PreUpdateEventArgs           $args
     * @param Doctrine\ORM\EntityManagerInterface             $em
     * @param Doctrine\ORM\UnitOfWork                         $uow
     * @param Knp\RadBundle\Security\RecoverableUserInterface $entity
     */
    function its_preUpdate_should_erase_password_recovery_key_for_recoverable_user($args, $em, $uow, $entity)
    {
        $args->getEntity()->willReturn($entity);

        $args->getEntityManager()->willReturn($em);
        $em->getUnitOfWork()->willReturn($uow);
        $uow->computeChangeSets()->shouldBeCalled();

        $entity->getPlainPassword()->willReturn('custom_pass');
        $entity->getSalt()->willReturn('some_salt');
        $entity->getPassword()->willReturn('custom_pass#some_salt');

        $entity->setPassword('custom_pass#some_salt')->shouldBeCalled();
        $entity->eraseCredentials()->shouldBeCalled();
        $entity->erasePasswordRecoveryKey()->shouldBeCalled();

        $this->preUpdate($args);
    }

    /**
     * @param Doctrine\ORM\Event\PreUpdateEventArgs $args
     * @param Knp\RadBundle\Security\UserInterface  $entity
     */
    function its_preUpdate_should_not_touch_entity_if_password_is_not_updated($args, $entity)
    {
        $args->getEntity()->willReturn($entity);
        $args->getEntityManager()->shouldNotBeCalled();

        $entity->getPlainPassword()->willReturn(null);
        $entity->setPassword(\Prophecy\Argument::any())->shouldNotBeCalled();

        $this->preUpdate($args);
    }

    /**
     * @param Doctrine\ORM\Event\PreUpdateEventArgs $args
     * @param stdClass                              $entity
     */
    function its_preUpdate_should_not_touch_entities_without_interface($args, $entity)
    {
        $args->getEntity()->willReturn($entity);

        $this->preUpdate($args);
    }
}
