<?php

namespace Knp\RadBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterRoutePartBuilderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('knp_rad.routing.resolver')) {
            return;
        }

        $definition = $container->getDefinition('knp_rad.routing.resolver');

        $taggedServices = $container->findTaggedServiceIds('knp_rad.routing.part_builder');
        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall(
                'addPartBuilder',
                array(new Reference($id))
            );
        }
    }
}
