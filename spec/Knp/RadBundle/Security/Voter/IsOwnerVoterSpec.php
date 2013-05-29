<?php

namespace spec\Knp\RadBundle\Security\Voter;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class IsOwnerVoterSpec extends ObjectBehavior
{
    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     **/
    function let()
    {
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Knp\RadBundle\Security\Voter\IsOwnerVoter');
    }

    function it_should_only_support_IS_OWNER_attribute()
    {
        $this->supportsAttribute('IS_OWNER')->shouldReturn(true);
        $this->supportsAttribute('IS_SOMETHING_ELSE')->shouldReturn(false);
    }

    /**
     * @param Knp\RadBundle\Security\OwnerInterface   $user
     * @param Knp\RadBundle\Security\OwnableInterface $object
     **/
    function it_should_vote_yes_for_owned_object($token, $user, $object)
    {
        $token->getUser()->willReturn($user);
        $object->getOwner()->willReturn($user);
        $this->vote($token, $object, array('IS_OWNER'))->shouldReturn(VoterInterface::ACCESS_GRANTED);
    }

    /**
     * @param Knp\RadBundle\Security\OwnerInterface   $user
     * @param Knp\RadBundle\Security\OwnerInterface   $otherUser
     * @param Knp\RadBundle\Security\OwnableInterface $object
     **/
    function it_should_vote_no_for_not_owned_object($token, $user, $otherUser, $object)
    {
        $token->getUser()->willReturn($user);
        $object->getOwner()->willReturn($otherUser);
        $this->vote($token, $object, array('IS_OWNER'))->shouldReturn(VoterInterface::ACCESS_DENIED);
    }

    /**
     * @param Knp\RadBundle\Security\OwnerInterface   $user
     * @param stdClass $object
     **/
    function it_should_abstain_to_vote_for_not_ownable_object($token, $user, $object)
    {
        $token->getUser()->willReturn($user);
        $this->vote($token, $object, array('IS_OWNER'))->shouldReturn(VoterInterface::ACCESS_ABSTAIN);
    }

    /**
     * @param stdClass  $user
     * @param Knp\RadBundle\Security\OwnableInterface $object
     **/
    function it_should_abstain_to_vote_for_not_owner_token_user($token, $user, $object)
    {
        $token->getUser()->willReturn($user);
        $object->getOwner()->willReturn($user);
        $this->vote($token, $object, array('IS_OWNER'))->shouldReturn(VoterInterface::ACCESS_ABSTAIN);
    }

    /**
     * @param Knp\RadBundle\Security\OwnerInterface   $user
     * @param Knp\RadBundle\Security\OwnableInterface $object
     **/
    function it_should_abstain_to_vote_for_unknown_attribute($token, $user, $object)
    {
        $token->getUser()->willReturn($user);
        $object->getOwner()->willReturn($user);
        $this->vote($token, $object, array('IS_TEST'))->shouldReturn(VoterInterface::ACCESS_ABSTAIN);
    }

    /**
     * @param Knp\RadBundle\Security\OwnableInterface $object
     * @param Symfony\Component\Security\Core\User\UserInterface $equatableUser
     * @param stdClass $user
     **/
    function it_should_vote_yes_for_equal_owners($token, $user, $object, $equatableUser)
    {
        $user->implement('Knp\RadBundle\Security\OwnerInterface');
        $user->implement('Symfony\Component\Security\Core\User\EquatableInterface');

        $token->getUser()->willReturn($user);
        $object->getOwner()->willReturn($equatableUser);
        $user->isEqualTo($equatableUser->getWrappedObject())->willReturn(true);

        $this->vote($token, $object, array('IS_OWNER'))->shouldReturn(VoterInterface::ACCESS_GRANTED);
    }

    /**
     * @param Knp\RadBundle\Security\OwnableInterface $object
     * @param Symfony\Component\Security\Core\User\UserInterface $equatableUser
     * @param stdClass $user
     **/
    function it_should_vote_no_for_non_equal_owners($token, $user, $object, $equatableUser)
    {
        $user->implement('Knp\RadBundle\Security\OwnerInterface');
        $user->implement('Symfony\Component\Security\Core\User\EquatableInterface');

        $token->getUser()->willReturn($user);
        $object->getOwner()->willReturn($equatableUser);
        $user->isEqualTo($equatableUser->getWrappedObject())->willReturn(false);

        $this->vote($token, $object, array('IS_OWNER'))->shouldReturn(VoterInterface::ACCESS_DENIED);
    }

    /**
     * @param Knp\RadBundle\Security\OwnableInterface $object
     * @param Symfony\Component\Security\Core\User\UserInterface $equatableUser
     * @param stdClass $user
     **/
    function it_should_use_isEqualTo_if_possible($token, $user, $object, $equatableUser)
    {
        $user->implement('Knp\RadBundle\Security\OwnerInterface');
        $user->implement('Symfony\Component\Security\Core\User\EquatableInterface');

        $token->getUser()->willReturn($user);
        $object->getOwner()->willReturn($equatableUser);
        $user->isEqualTo($equatableUser->getWrappedObject())->shouldBeCalled();

        $this->vote($token, $object, array('IS_OWNER'))->shouldReturn(VoterInterface::ACCESS_DENIED);
    }

    /**
     * @param Knp\RadBundle\Security\OwnableInterface $object
     * @param Knp\RadBundle\Security\OwnerInterface $nonEquatableUser
     * @param stdClass $user
     **/
    function it_should_not_use_isEqualTo_if_no_UserInterface($token, $user, $object, $nonEquatableUser)
    {
        $user->implement('Knp\RadBundle\Security\OwnerInterface');
        $user->implement('Symfony\Component\Security\Core\User\EquatableInterface');

        $token->getUser()->willReturn($user);
        $object->getOwner()->willReturn($nonEquatableUser);
        $user->isEqualTo($nonEquatableUser)->shouldNotBeCalled();

        $this->vote($token, $object, array('IS_OWNER'))->shouldReturn(VoterInterface::ACCESS_DENIED);
    }

    /**
     * @param Knp\RadBundle\Security\OwnableInterface $object
     * @param Symfony\Component\Security\Core\User\UserInterface $equatableUser
     * @param Knp\RadBundle\Security\OwnerInterface $user
     **/
    function it_should_not_use_isEqualTo_if_no_EquatableInterface($token, $user, $object, $equatableUser)
    {
        $token->getUser()->willReturn($user);
        $object->getOwner()->willReturn($equatableUser);

        $this->vote($token, $object, array('IS_OWNER'))->shouldReturn(VoterInterface::ACCESS_DENIED);
    }
}
