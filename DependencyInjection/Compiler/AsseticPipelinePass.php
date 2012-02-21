<?php

/*
 * This file is part of the KnpRadBundle package.
 *
 * (c) KnpLabs <http://knplabs.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Knp\Bundle\RadBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Enables assetic pipeline support.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class AsseticPipelinePass implements CompilerPassInterface
{
    /**
     * Adds asset references to the asset manager.
     *
     * @param ContainerBuilder $container Container instance
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('assetic.asset_factory')) {
            return;
        }
        if (!$container->hasDefinition('knp_rad.assetic_pipeline.locator')) {
            return;
        }

        $factory = $container->getDefinition('assetic.asset_factory');
        $factory->setClass('Knp\\Bundle\\RadBundle\\Assetic\\PipelineAssetFactory');
        $factory->addMethodCall('setPipelineAssetLocator', array(
            new Reference('knp_rad.assetic_pipeline.locator')
        ));

        $filters = array();
        foreach ($container->findTaggedServiceIds('assetic.filter') as $id => $attrs) {
            foreach ($attrs as $attr) {
                if (isset($attr['alias'])) {
                    $filters[] = $attr['alias'];
                }
            }
        }
        $container->setParameter('knp_rad.assetic_pipeline.locator.filters', $filters);
    }
}
