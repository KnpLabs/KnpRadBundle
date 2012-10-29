<?php

namespace Knp\RadBundle\AppBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle as BaseBundle;

class Bundle extends BaseBundle
{
    public function getContainerExtension()
    {
        if ($extension = parent::getContainerExtension()) {
            return $extension;
        }

        return $this->extension = new ContainerExtension(__DIR__);
    }
}
