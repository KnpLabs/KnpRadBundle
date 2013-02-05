<?php

namespace Knp\RadBundle\DependencyInjection\Definition;

use Symfony\Component\DependencyInjection\Definition;

class ValidatorConstraintDefinitionFactory extends AbstractContainerAwareFactory
{
    public function createDefinition($className)
    {
        $definition = new Definition($className);
        $definition->addTag('validator.constraint_validator');
        $definition->setPublic(true);

        $this->injectContainer($definition);

        return $definition;
    }
}
