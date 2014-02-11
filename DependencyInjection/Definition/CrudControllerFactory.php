<?php

namespace Knp\RadBundle\DependencyInjection\Definition;

use Symfony\Component\DependencyInjection\Definition;

class CrudControllerFactory extends AbstractContainerAwareFactory
{
    public function createDefinition($className)
    {
        $definition = new Definition($className);

        $this->injectContainer($definition);

        return $definition;
    }
}
