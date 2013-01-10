<?php

namespace spec\Knp\RadBundle\DependencyInjection\Compiler;

use PHPSpec2\ObjectBehavior;

class RegisterFormTypesCompilerPass extends ObjectBehavior
{
    /**
     * @param Symfony\Component\HttpKernel\Bundle\BundleInterface $bundle
     * @param Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param Knp\RadBundle\Finder\ClassFinder $classFinder
     * @param Knp\RadBundle\DependencyInjection\Definition\FormTypeDefinitionFactory $definitionFactory
     * @param Symfony\Component\DependencyInjection\Definition $formExtension
     * @param Knp\RadBundle\DependencyInjection\ServiceIdGenerator $servIdGen
     */
    function let($bundle, $classFinder, $definitionFactory, $container, $formExtension, $servIdGen)
    {
        $this->beConstructedWith($bundle, $classFinder, $definitionFactory, $servIdGen);

        $bundle->getPath()->shouldBeCalled()->willReturn('/my/project/src/App');
        $bundle->getNamespace()->shouldBeCalled()->willReturn('App');

        $classFinder->findClassesMatching('/my/project/src/App/Form', 'App\Form', 'Type$')->shouldBeCalled()->willReturn(array(
            'App\Form\CheeseType',
            'App\Form\EditCheeseType',
            'App\Form\MouseType',
        ));

        $servIdGen->generateForBundleClass($bundle, 'App\Form\CheeseType')->willReturn('app.form.cheese_type');
        $servIdGen->generateForBundleClass($bundle, 'App\Form\EditCheeseType')->willReturn('app.form.edit_cheese_type');
        $servIdGen->generateForBundleClass($bundle, 'App\Form\MouseType')->willReturn('app.form.mouse_type');

        $formExtension->getArgument(1)->willReturn(array());
        $container->getDefinition('form.extension')->willReturn($formExtension);
    }

    /**
     * @param Symfony\Component\DependencyInjection\Definition $cheeseTypeDef
     * @param Symfony\Component\DependencyInjection\Definition $editCheeseTypeDef
     * @param Symfony\Component\DependencyInjection\Definition $mouseTypeDef
     **/
    function it_should_add_tagged_service_for_each_form_type($container, $classFinder, $definitionFactory, $cheeseTypeDef, $editCheeseTypeDef, $mouseTypeDef)
    {
        $container->hasDefinition('app.form.cheese_type')->willReturn(false);
        $definitionFactory->createDefinition('App\Form\CheeseType')->shouldBeCalled()->willReturn($cheeseTypeDef);
        $container->setDefinition('app.form.cheese_type', $cheeseTypeDef)->shouldBeCalled();

        $container->hasDefinition('app.form.edit_cheese_type')->willReturn(false);
        $definitionFactory->createDefinition('App\Form\EditCheeseType')->willReturn($editCheeseTypeDef);
        $container->setDefinition('app.form.edit_cheese_type', $editCheeseTypeDef)->shouldBeCalled();

        $container->hasDefinition('app.form.mouse_type')->willReturn(false);
        $definitionFactory->createDefinition('App\Form\MouseType')->willReturn($mouseTypeDef);
        $container->setDefinition('app.form.mouse_type', $mouseTypeDef)->shouldBeCalled();

        $this->process($container);
    }

    /**
     * @param Symfony\Component\DependencyInjection\Definition $cheeseTypeDef
     * @param Symfony\Component\DependencyInjection\Definition $editCheeseTypeDef
     * @param Symfony\Component\DependencyInjection\Definition $mouseTypeDef
     **/
    function it_should_not_add_service_with_same_id_and_tag_alias($container, $classFinder, $definitionFactory, $cheeseTypeDef, $editCheeseTypeDef, $mouseTypeDef)
    {
        $container->hasDefinition('app.form.cheese_type')->willReturn(false);
        $definitionFactory->createDefinition('App\Form\CheeseType')->shouldBeCalled()->willReturn($cheeseTypeDef);
        $container->setDefinition('app.form.cheese_type', $cheeseTypeDef)->shouldBeCalled();

        $container->hasDefinition('app.form.edit_cheese_type')->willReturn(true);
        $container->setDefinition('app.form.edit_cheese_type', $editCheeseTypeDef)->shouldNotBeCalled();

        $container->hasDefinition('app.form.mouse_type')->willReturn(true);
        $container->setDefinition('app.form.mouse_type', $mouseTypeDef)->shouldNotBeCalled();

        $this->process($container);
    }
}
