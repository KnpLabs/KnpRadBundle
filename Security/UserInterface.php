<?php

namespace Knp\RadBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;

interface UserInterface extends BaseUserInterface
{
    public function getPlainPassword();
}
