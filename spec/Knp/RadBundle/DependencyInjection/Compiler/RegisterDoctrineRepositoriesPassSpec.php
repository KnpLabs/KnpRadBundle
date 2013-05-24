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
    function let($bundle, $container, $definitionFactory, $serviceIdGenerator)
    {
        $this->beConstructedWith($bundle, $definitionFactory, $serviceIdGenerator);
    }

    function it_should_be_a_compiler_pass()
    {
        $this->shouldBeAnInstanceOf('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface');
    }

    /**
     * @param Doctrine\ORM\EntityManager $entityManager
     * @param Doctrine\DBAL\Connection $configuration
     * @param Doctrine\ORM\Mapping\Driver\DatabaseDriver $driverImpl
     * @param Symfony\Component\DependencyInjection\Definition $cheeseDef
     * @param Symfony\Component\DependencyInjection\Definition $customerDef
     */
    function it_should_register_a_repository_service_for_all_entities_found_in_the_bundle($container, $bundle, $definitionFactory, $serviceIdGenerator, $entityManager, $configuration, $driverImpl, $cheeseDef, $customerDef)
    {
        $bundle->getNamespace()->shouldBeCalled()->willReturn('App');

        $container->has('doctrine.orm.entity_manager')->willReturn(true)->shouldBeCalled();

        $container->get('doctrine.orm.entity_manager')->willReturn($entityManager)->shouldBeCalled();
        $entityManager->getConfiguration()->willReturn($configuration)->shouldBeCalled();
        $configuration->getMetadataDriverImpl()->willReturn($driverImpl)->shouldBeCalled();
        $driverImpl->getAllClassNames()->shouldBeCalled()->willReturn(array('App\Entity\Cheese', 'App\Entity\Customer'));

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
     * @param Doctrine\ORM\EntityManager $entityManager
     * @param Doctrine\DBAL\Connection $configuration
     * @param Doctrine\ORM\Mapping\Driver\DatabaseDriver $driverImpl
     * @param Symfony\Component\DependencyInjection\Definition $cheeseDef
     * @param Symfony\Component\DependencyInjection\Definition $customerDef
     */
    function it_should_not_register_a_repository_service_if_already_defined($container, $bundle, $definitionFactory, $serviceIdGenerator, $entityManager, $configuration, $driverImpl, $cheeseDef, $customerDef)
    {
        $bundle->getNamespace()->shouldBeCalled()->willReturn('App');

        $container->has('doctrine.orm.entity_manager')->willReturn(true)->shouldBeCalled();

        $container->get('doctrine.orm.entity_manager')->willReturn($entityManager)->shouldBeCalled();
        $entityManager->getConfiguration()->willReturn($configuration)->shouldBeCalled();
        $configuration->getMetadataDriverImpl()->willReturn($driverImpl)->shouldBeCalled();
        $driverImpl->getAllClassNames()->shouldBeCalled()->willReturn(array('App\Entity\Cheese', 'App\Entity\Customer'));

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
