<?php

namespace Knp\RadBundle\Security\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Knp\RadBundle\Security\OwnerInterface;

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

            if ($object->getOwner() === $token->getUser()) {
                return self::ACCESS_GRANTED;
            }

            return self::ACCESS_DENIED;
        }

        return self::ACCESS_ABSTAIN;
    }
}
