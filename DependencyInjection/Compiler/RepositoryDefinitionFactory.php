<?php

namespace Knp\RadBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Definition;

class RepositoryDefinitionFactory
{
    public function createDefinition($className)
    {
        $definition = new Definition();
        $definition->setPublic(false);
        $definition->setClass('Doctrine\Common\Persistence\ObjectRepository');
        $definition->setFactoryService('doctrine');
        $definition->setFactoryMethod('getRepository');
        $definition->setArguments(array($className));

        return $definition;
    }
}
