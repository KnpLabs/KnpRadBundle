<?php

namespace Knp\RadBundle\Security;

interface RecoverableUserInterface extends UserInterface
{
    public function erasePasswordRecoveryKey();
}
