<?php

namespace Knp\RadBundle\AppBundle;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    private $bundle;
    private $treeBuilderFactory;

    public function __construct(ConfigurableBundleInterface $bundle, TreeBuilderFactory $treeBuilderFactory = null)
    {
        $this->bundle = $bundle;
        $this->treeBuilderFactory = $treeBuilderFactory ?: new TreeBuilderFactory;
    }

    public function getBundle()
    {
        return $this->bundle;
    }

    public function getConfigTreeBuilder()
    {
        $treeBuilder = $this->treeBuilderFactory->createTreeBuilder();
        $alias       = $this->bundle->getContainerExtension()->getAlias();
        $rootNode    = $treeBuilder->root($alias);
        
        $this->bundle->buildConfiguration($rootNode);

        return $treeBuilder;
    }
}
