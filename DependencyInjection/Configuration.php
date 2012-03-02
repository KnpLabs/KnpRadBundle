<?php

/*
 * This file is part of the KnpRadBundle package.
 *
 * (c) KnpLabs <http://knplabs.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Knp\Bundle\RadBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * Configuration for the rad bundle
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('knp_rad');
        $rootNode
            ->children()
                ->arrayNode('assetic_pipeline')
                    ->addDefaultsIfNotSet()
                    ->fixXmlConfig('path')
                    ->fixXmlConfig('bundle')
                    ->children()
                        ->booleanNode('enabled')
                            ->defaultFalse()
                        ->end()
                        ->arrayNode('paths')->prototype('scalar')->end()->end()
                        ->arrayNode('bundles')->prototype('scalar')->end()->end()
                    ->end()
                ->end()
                ->booleanNode('application_routing')
                    ->defaultFalse()
                ->end()
                ->booleanNode('application_shortaction')
                    ->defaultFalse()
                ->end()
                ->booleanNode('application_views')
                    ->defaultFalse()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
