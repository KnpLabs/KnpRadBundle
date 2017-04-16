<?php

namespace Knp\RadBundle\DependencyInjection\Compiler\Routing;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ServiceBased implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $configs = array();
        foreach ($container->findTaggedServiceIds('knp_rad.route') as $id => $tags) {
            foreach ($tags as $tag) {
                $configs[$id] = $tag;
            }
        }

        $container->getDefinition('knp_rad.routing.service_based.loader')->replaceArgument(0, $configs);
    }
}
