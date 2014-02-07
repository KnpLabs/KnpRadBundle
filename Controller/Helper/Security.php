<?php

namespace Knp\RadBundle\Controller\Helper;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContextInterface;

class Security
{
    private $securityContext;

    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public function createAccessDeniedException($message = 'Access Denied', \Exception $previous = null)
    {
        return new AccessDeniedException($message, $previous);
    }

    public function isGranted($attributes, $object = null)
    {
        return $this->getSecurity()->isGranted($attributes, $object);
    }

    public function isGrantedOr403($attributes, $object = null)
    {
        if (!$this->isGranted($attributes, $object)) {
            throw $this->createAccessDeniedException();
        }
    }

    public function getSecurity()
    {
        return $this->securityContext;
    }
}
