<?php

namespace Knp\RadBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
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
                ->arrayNode('listener')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('view')->defaultTrue()->end()
                        ->booleanNode('resolver')->defaultTrue()->end()
                    ->end()
                ->end()
                ->booleanNode('routing_loader')->defaultTrue()->end()
                ->booleanNode('form')->defaultTrue()->end()
                ->booleanNode('datatable')->defaultTrue()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
