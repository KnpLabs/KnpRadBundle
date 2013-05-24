<?php

namespace spec\Knp\RadBundle\AppBundle;

use PHPSpec2\ObjectBehavior;

class Configuration extends ObjectBehavior
{
    /**
     * @param Knp\RadBundle\AppBundle\ConfigurableBundleInterface $bundle
     * @param Knp\RadBundle\AppBundle\TreeBuilderFactory          $treeBuilderFactory
     */
    function let($bundle, $treeBuilderFactory)
    {
        $this->beConstructedWith($bundle, $treeBuilderFactory);
    }

    function it_should_have_bundle_accessor($bundle)
    {
        $this->getBundle()->shouldReturn($bundle);
    }

    /**
     * @param Symfony\Component\DependencyInjection\Extension\ExtensionInterface $containerExtension
     * @param Symfony\Component\Config\Definition\Builder\TreeBuilder            $treeBuilder
     * @param Symfony\Component\Config\Definition\Builder\NodeParentInterface    $rootNode
     */
    function its_getConfigTreeBuilder_should_use_bundle_to_build_tree($bundle, $containerExtension, $treeBuilderFactory, $treeBuilder, $rootNode)
    {
        $bundle->getContainerExtension()->willReturn($containerExtension);
        $containerExtension->getAlias()->willReturn('some_alias');

        $treeBuilderFactory->createTreeBuilder()->willReturn($treeBuilder);
        $treeBuilder->root('some_alias')->willReturn($rootNode);

        $bundle->buildConfiguration($rootNode)->shouldBeCalled();

        $this->getConfigTreeBuilder()->shouldReturn($treeBuilder);
    }
}
