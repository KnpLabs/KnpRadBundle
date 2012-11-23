<?php

namespace spec\Knp\RadBundle\DependencyInjection\Compiler;

use PHPSpec2\ObjectBehavior;

class RegisterDoctrineRepositoriesPass extends ObjectBehavior
{
    /**
     * @param Symfony\Component\HttpKernel\Bundle\BundleInterface $bundle
     * @param Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param Knp\RadBundle\Finder\ClassFinder $classFinder
     * @param Knp\RadBundle\DependencyInjection\Definition\DoctrineRepositoryFactory $definitionFactory
     */
    function let($bundle, $container, $classFinder, $definitionFactory)
    {
        $this->beConstructedWith($bundle, $classFinder, $definitionFactory);
    }

    function it_should_be_a_compiler_pass()
    {
        $this->shouldBeAnInstanceOf('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface');
    }

    /**
     * @param Symfony\Component\DependencyInjection\Definition $cheeseDef
     * @param Symfony\Component\DependencyInjection\Definition $customerDef
     */
    function it_should_register_a_repository_service_for_all_entities_found_in_the_bundle($container, $bundle, $classFinder, $definitionFactory, $cheeseDef, $customerDef)
    {
        $bundle->getPath()->shouldBeCalled()->willReturn('/my/project/src/App');
        $bundle->getNamespace()->shouldBeCalled()->willReturn('App');

        $classFinder->findClassesMatching('/my/project/src/App/Entity', 'App\Entity', '(?<!Repository)$')->shouldBeCalled()->willReturn(array('App\Entity\Cheese', 'App\Entity\Customer'));

        $container->hasDefinition('orm.cheese_repository')->willReturn(false)->shouldBeCalled();
        $container->hasDefinition('orm.customer_repository')->willReturn(false)->shouldBeCalled();

        $definitionFactory->createDefinition('App\Entity\Cheese')->shouldBeCalled()->willReturn($cheeseDef);
        $definitionFactory->createDefinition('App\Entity\Customer')->shouldBeCalled()->willReturn($customerDef);

        $container->setDefinition('orm.cheese_repository', $cheeseDef)->shouldBeCalled();
        $container->setDefinition('orm.customer_repository', $customerDef)->shouldBeCalled();

        $this->process($container);
    }

    /**
     * @param Symfony\Component\DependencyInjection\Definition $cheeseDef
     * @param Symfony\Component\DependencyInjection\Definition $customerDef
     */
    function it_should_not_register_a_repository_service_if_already_defined($container, $bundle, $classFinder, $definitionFactory, $cheeseDef, $customerDef)
    {
        $bundle->getPath()->shouldBeCalled()->willReturn('/my/project/src/App');
        $bundle->getNamespace()->shouldBeCalled()->willReturn('App');

        $classFinder->findClassesMatching('/my/project/src/App/Entity', 'App\Entity', '(?<!Repository)$')->shouldBeCalled()->willReturn(array('App\Entity\Cheese', 'App\Entity\Customer'));

        $container->hasDefinition('orm.cheese_repository')->willReturn(true)->shouldBeCalled();
        $container->hasDefinition('orm.customer_repository')->willReturn(false)->shouldBeCalled();

        $definitionFactory->createDefinition('App\Entity\Cheese')->shouldNotBeCalled();
        $definitionFactory->createDefinition('App\Entity\Customer')->shouldBeCalled()->willReturn($customerDef);

        $container->setDefinition('orm.cheese_repository', $cheeseDef)->shouldNotBeCalled();
        $container->setDefinition('orm.customer_repository', $customerDef)->shouldBeCalled();

        $this->process($container);
    }
    /**
     * @param Symfony\Component\DependencyInjection\Definition $definition
     */
    function it_should_underscore_camelcased_names($container, $bundle, $classFinder, $definitionFactory, $definition)
    {
        $bundle->getPath()->shouldBeCalled()->willReturn('/my/project/src/App');
        $bundle->getNamespace()->shouldBeCalled()->willReturn('App');

        $classFinder->findClassesMatching('/my/project/src/App/Entity', 'App\Entity', '(?<!Repository)$')->shouldBeCalled()->willReturn(array('App\Entity\CheesePicture'));
        $container->hasDefinition('orm.cheese_picture_repository')->willReturn(false)->shouldBeCalled();

        $definitionFactory->createDefinition('App\Entity\CheesePicture')->shouldBeCalled()->willReturn($definition);

        $container->setDefinition('orm.cheese_picture_repository', $definition)->shouldBeCalled();

        $this->process($container);
    }

    /**
     * @param Symfony\Component\DependencyInjection\Definition $definition
     */
    function it_should_replace_namespace_separatores_by_dots_in_names($container, $bundle, $classFinder, $definitionFactory, $definition)
    {
        $bundle->getPath()->shouldBeCalled()->willReturn('/my/project/src/App');
        $bundle->getNamespace()->shouldBeCalled()->willReturn('App');

        $classFinder->findClassesMatching('/my/project/src/App/Entity', 'App\Entity', '(?<!Repository)$')->shouldBeCalled()->willReturn(array('App\Entity\Cheese\Picture'));
        $container->hasDefinition('orm.cheese.picture_repository')->willReturn(false)->shouldBeCalled();

        $definitionFactory->createDefinition('App\Entity\Cheese\Picture')->shouldBeCalled()->willReturn($definition);

        $container->setDefinition('orm.cheese.picture_repository', $definition)->shouldBeCalled();

        $this->process($container);
    }
}
