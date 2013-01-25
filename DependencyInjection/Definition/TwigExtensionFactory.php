<?php

namespace Knp\RadBundle\DependencyInjection\Definition;

use Symfony\Component\DependencyInjection\Definition;

class TwigExtensionFactory extends AbstractContainerAwareFactory
{
    public function createDefinition($className)
    {
        $definition = new Definition($className);
        $definition->addTag('twig.extension');
        $definition->setPublic(false);

        $this->injectContainer($definition);

        return $definition;
    }
}
