<?php

namespace Knp\RadBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Definition;

class TwigExtensionServiceFactory
{
    public function createDefinition($className)
    {
        $definition = new Definition($className);
        $definition->setPublic(false);

        return $definition;
    }
}
