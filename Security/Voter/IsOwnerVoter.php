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
        $refl = new \ReflectionClass($class);

        return $refl->implementsInterface('Knp\RadBundle\Security\OwnableInterface');
    }

    public function vote(TokenInterface $token, $object, array $attributes)
    {
        foreach ($attributes as $attribute) {
            if (!$this->supportsAttribute($attribute)) {
                continue;
            }

            if (!$this->supportsClass(get_class($object))) {
                return self::ACCESS_DENIED;
            }

            if (!$token->getUser() instanceof OwnerInterface) {
                return self::ACCESS_DENIED;
            }

            if ($object->getOwner() === $token->getUser()) {
                return self::ACCESS_GRANTED;
            }

            return self::ACCESS_DENIED;
        }

        return self::ACCESS_ABSTAIN;
    }
}
