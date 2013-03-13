<?php

namespace Knp\RadBundle\Security\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Knp\RadBundle\Security\OwnerInterface;
use Knp\RadBundle\Security\OwnableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class IsOwnerVoter implements VoterInterface
{
    const IS_OWNER = 'IS_OWNER';

    public function supportsAttribute($attribute)
    {
        return self::IS_OWNER === $attribute;
    }

    public function supportsClass($class)
    {
        if (is_object($class)) {
            $refl = new \ReflectionObject($class);

            return $refl->implementsInterface('Knp\RadBundle\Security\OwnableInterface');
        }

        return false;
    }

    public function vote(TokenInterface $token, $object, array $attributes)
    {
        foreach ($attributes as $attribute) {
            if (!$this->supportsAttribute($attribute)) {
                continue;
            }

            if (!$this->supportsClass($object)) {
                return self::ACCESS_ABSTAIN;
            }

            if (!$token->getUser() instanceof OwnerInterface) {
                return self::ACCESS_ABSTAIN;
            }

            if ($this->isOwner($token->getUser(), $object)) {
                return self::ACCESS_GRANTED;
            }

            return self::ACCESS_DENIED;
        }

        return self::ACCESS_ABSTAIN;
    }

    private function isOwner(OwnerInterface $owner, OwnableInterface $ownable)
    {
        if ($ownable->getOwner() instanceof UserInterface && $owner instanceof EquatableInterface) {
            return $owner->isEqualTo($ownable->getOwner());
        }

        return $ownable->getOwner() === $owner;
    }
}
