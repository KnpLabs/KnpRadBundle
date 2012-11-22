<?php

namespace Knp\RadBundle\DependencyInjection\Definition;

use Symfony\Component\DependencyInjection\Definition;

class FormTypeDefinitionFactory extends AbstractContainerAwareFactory
{
    public function createDefinition($className)
    {
        $definition = new Definition($className);
        $definition->addTag('form.type');
        $definition->setPublic(true);

        $this->injectContainer($definition);

        return $definition;
    }
}
