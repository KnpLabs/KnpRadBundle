<?php

namespace Knp\RadBundle\DependencyInjection\Definition;

use Symfony\Component\DependencyInjection\Definition;

class FormTypeDefinitionFactory
{
    public function create($class)
    {
        $definition = new Definition($class);
        $definition->addTag('form.type');

        return $definition;
    }
}

