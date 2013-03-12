<?php

namespace spec\Knp\RadBundle\Security\Voter;

use PHPSpec2\ObjectBehavior;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class IsOwnerVoter extends ObjectBehavior
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
    function it_should_vote_no_for_not_ownable_object($token, $user, $object)
    {
        $token->getUser()->willReturn($user);
        $object->getOwner()->willReturn($user);
        $this->vote($token, $object, array('IS_OWNER'))->shouldReturn(VoterInterface::ACCESS_DENIED);
    }

    /**
     * @param stdClass  $user
     * @param Knp\RadBundle\Security\OwnableInterface $object
     **/
    function it_should_vote_no_for_not_owner_token_user($token, $user, $object)
    {
        $token->getUser()->willReturn($user);
        $object->getOwner()->willReturn($user);
        $this->vote($token, $object, array('IS_OWNER'))->shouldReturn(VoterInterface::ACCESS_DENIED);
    }

    /**
     * @param Knp\RadBundle\Security\OwnerInterface   $user
     * @param Knp\RadBundle\Security\OwnableInterface $object
     **/
    function it_should_abstain_to_vote_for_unkown_attribute($token, $user, $object)
    {
        $token->getUser()->willReturn($user);
        $object->getOwner()->willReturn($user);
        $this->vote($token, $object, array('IS_TEST'))->shouldReturn(VoterInterface::ACCESS_ABSTAIN);
    }
}
