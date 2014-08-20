<?php

namespace Knp\RadBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterAliceProvidersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $collectionDefinition = $container->getDefinition('knp_rad.alice.provider_collection');
        foreach (array_keys($container->findTaggedServiceIds('alice.provider')) as $id) {
            $collectionDefinition->addMethodCall('addProvider', array(new Reference($id)));
        }
    }
}
