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
                ->arrayNode('mailer')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('logger')->defaultFalse()->end()
                        ->booleanNode('message_factory')->defaultTrue()->end()
                    ->end()
                ->end()
                ->arrayNode('listener')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('view')->defaultTrue()->end()
                        ->booleanNode('resource_resolver')->defaultTrue()->end()
                        ->booleanNode('orm_user')->defaultTrue()->end()
                        ->booleanNode('exception_rethrow')->defaultFalse()->end()
                    ->end()
                ->end()
                ->booleanNode('routing_loader')->defaultTrue()->end()
                ->booleanNode('datatable')->defaultTrue()->end()
                ->arrayNode('flashes')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->scalarNode('trans_catalog')->defaultValue('messages')->end()
                    ->end()
                ->end()
                ->arrayNode('csrf_links')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultFalse()->end()
                        ->scalarNode('intention')->defaultValue('link')->end()
                    ->end()
                ->end()
                ->arrayNode('security')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->booleanNode('voter')->defaultTrue()->end()
                        ->scalarNode('decision_manager')->defaultValue('security.access.decision_manager')->end()
                    ->end()
                ->end()
                ->arrayNode('templating')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                    ->end()
                ->end()
                ->arrayNode('form')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->booleanNode('manager')->defaultTrue()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
