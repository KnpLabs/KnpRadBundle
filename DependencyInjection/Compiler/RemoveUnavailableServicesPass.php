<?php

namespace Knp\RadBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RemoveUnavailableServicesPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        foreach ($container->findTaggedServiceIds('remove-when-missing') as $id => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['service'])) {
                    continue;
                }

                if ($container->hasDefinition($tag['service'])) {
                    continue;
                }
                if ($container->hasAlias($tag['service'])) {
                    continue;
                }

                $container->removeDefinition($id);
            }
        }
    }
}
