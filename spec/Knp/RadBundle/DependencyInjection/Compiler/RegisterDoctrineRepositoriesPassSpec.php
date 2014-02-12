<?php

namespace spec\Knp\RadBundle\DependencyInjection\Compiler;

use PhpSpec\ObjectBehavior;

class RegisterDoctrineRepositoriesPassSpec extends ObjectBehavior
{
    /**
     * @param Symfony\Component\HttpKernel\Bundle\BundleInterface                    $bundle
     * @param Symfony\Component\DependencyInjection\ContainerBuilder                 $container
     * @param Knp\RadBundle\Finder\ClassFinder                                       $classFinder
     * @param Knp\RadBundle\DependencyInjection\Definition\DoctrineRepositoryFactory $definitionFactory
     * @param Knp\RadBundle\DependencyInjection\ServiceIdGenerator                   $serviceIdGenerator
     */
    function let($container, $bundle, $classFinder, $definitionFactory, $serviceIdGenerator)
    {
        $this->beConstructedWith($bundle, $classFinder, $definitionFactory, $serviceIdGenerator);

        $container->getParameter('knp_rad.detect.entity')->willReturn(true);
        $container->hasDefinition('doctrine')->willReturn(true);
        $bundle->getNamespace()->willReturn('App');
        $bundle->getPath()->willReturn('src/App');
        $classFinder->findClassesMatching('src/App/Entity', 'App\Entity', '(?<!Repository)$')->willReturn(array('App\Entity\Cheese', 'App\Entity\Customer'));
    }

    function it_should_be_a_compiler_pass()
    {
        $this->shouldBeAnInstanceOf('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface');
    }

    /**
     * @param Symfony\Component\DependencyInjection\Definition $cheeseDef
     * @param Symfony\Component\DependencyInjection\Definition $customerDef
     */
    function it_should_register_a_repository_service_for_all_entities_found_in_the_bundle($container, $bundle, $definitionFactory, $serviceIdGenerator, $cheeseDef, $customerDef)
    {
        $serviceIdGenerator->generateForBundleClass($bundle, 'App\Entity\Cheese', 'repository')->willReturn('app.entity.cheese_repository');
        $serviceIdGenerator->generateForBundleClass($bundle, 'App\Entity\Customer', 'repository')->willReturn('app.entity.customer_repository');

        $container->hasDefinition('app.entity.cheese_repository')->willReturn(false)->shouldBeCalled();
        $container->hasDefinition('app.entity.customer_repository')->willReturn(false)->shouldBeCalled();

        $definitionFactory->createDefinition('App\Entity\Cheese')->shouldBeCalled()->willReturn($cheeseDef);
        $definitionFactory->createDefinition('App\Entity\Customer')->shouldBeCalled()->willReturn($customerDef);

        $container->setDefinition('app.entity.cheese_repository', $cheeseDef)->shouldBeCalled();
        $container->setDefinition('app.entity.customer_repository', $customerDef)->shouldBeCalled();

        $this->process($container);
    }

    /**
     * @param Symfony\Component\DependencyInjection\Definition $cheeseDef
     * @param Symfony\Component\DependencyInjection\Definition $customerDef
     */
    function it_should_not_register_a_repository_service_if_already_defined($container, $bundle, $definitionFactory, $serviceIdGenerator, $cheeseDef, $customerDef)
    {
        $serviceIdGenerator->generateForBundleClass($bundle, 'App\Entity\Cheese', 'repository')->willReturn('app.entity.cheese_repository');
        $serviceIdGenerator->generateForBundleClass($bundle, 'App\Entity\Customer', 'repository')->willReturn('app.entity.customer_repository');

        $container->hasDefinition('app.entity.cheese_repository')->willReturn(true)->shouldBeCalled();
        $container->hasDefinition('app.entity.customer_repository')->willReturn(false)->shouldBeCalled();

        $definitionFactory->createDefinition('App\Entity\Cheese')->shouldNotBeCalled();
        $definitionFactory->createDefinition('App\Entity\Customer')->shouldBeCalled()->willReturn($customerDef);

        $container->setDefinition('app.entity.cheese_repository', $cheeseDef)->shouldNotBeCalled();
        $container->setDefinition('app.entity.customer_repository', $customerDef)->shouldBeCalled();

        $this->process($container);
    }
}
