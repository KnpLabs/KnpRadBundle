<?php

namespace spec\Knp\RadBundle\DependencyInjection\Compiler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RegisterFormTypesPassSpec extends ObjectBehavior
{
    /**
     * @param Symfony\Component\HttpKernel\Bundle\BundleInterface $bundle
     * @param Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param Knp\RadBundle\Finder\ClassFinder $classFinder
     * @param Knp\RadBundle\DependencyInjection\Definition\FormTypeDefinitionFactory $definitionFactory
     * @param Symfony\Component\DependencyInjection\Definition $formExtension
     * @param Knp\RadBundle\DependencyInjection\ServiceIdGenerator $servIdGen
     * @param Knp\RadBundle\Reflection\ReflectionFactory $reflectionFactory
     * @param ReflectionClass $reflClass
     */
    function let($bundle, $classFinder, $definitionFactory, $container, $formExtension, $servIdGen, $reflectionFactory, $reflClass)
    {
        $this->beConstructedWith($bundle, $classFinder, $definitionFactory, $servIdGen, $reflectionFactory);
        $container->getParameter('knp_rad.detect.form_type')->willReturn(true);

        $bundle->getPath()->willReturn('/my/project/src/App');
        $bundle->getNamespace()->willReturn('App');

        $classes = array(
            'App\Form\CheeseType',
            'App\Form\EditCheeseType',
            'App\Form\MouseType',
        );
        $classFinder->findClassesMatching('/my/project/src/App/Form', 'App\Form', 'Type$')->willReturn($classes);
        $classFinder->filterClassesImplementing($classes, 'Symfony\Component\Form\FormTypeInterface')->willReturn($classes);

        $servIdGen->generateForBundleClass($bundle, 'App\Form\CheeseType')->willReturn('app.form.cheese_type');
        $servIdGen->generateForBundleClass($bundle, 'App\Form\EditCheeseType')->willReturn('app.form.edit_cheese_type');
        $servIdGen->generateForBundleClass($bundle, 'App\Form\MouseType')->willReturn('app.form.mouse_type');

        $reflClass->isAbstract()->willReturn(false);
        $reflectionFactory->createReflectionClass(Argument::any())->willReturn($reflClass);

        $formExtension->getArgument(1)->willReturn(array());
        $container->getDefinition('form.extension')->willReturn($formExtension);
    }

    /**
     * @param Symfony\Component\DependencyInjection\Definition $cheeseTypeDef
     * @param Symfony\Component\DependencyInjection\Definition $editCheeseTypeDef
     * @param Symfony\Component\DependencyInjection\Definition $mouseTypeDef
     **/
    function it_should_add_tagged_service_for_each_form_type($container, $classFinder, $definitionFactory, $cheeseTypeDef, $editCheeseTypeDef, $mouseTypeDef, $formExtension)
    {
        $container->hasDefinition('form.extension')->willReturn(true);
        $formExtension->replaceArgument(1, \Prophecy\Argument::any())->shouldBeCalled();

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
    function it_should_not_add_service_with_same_id_and_tag_alias($container, $classFinder, $definitionFactory, $cheeseTypeDef, $editCheeseTypeDef, $mouseTypeDef, $formExtension)
    {
        $container->hasDefinition('form.extension')->willReturn(true);
        $formExtension->replaceArgument(\Prophecy\Argument::cetera())->shouldBeCalled();

        $container->hasDefinition('app.form.cheese_type')->willReturn(false);
        $definitionFactory->createDefinition('App\Form\CheeseType')->shouldBeCalled()->willReturn($cheeseTypeDef);
        $container->setDefinition('app.form.cheese_type', $cheeseTypeDef)->shouldBeCalled();

        $container->hasDefinition('app.form.edit_cheese_type')->willReturn(true);
        $container->setDefinition('app.form.edit_cheese_type', $editCheeseTypeDef)->shouldNotBeCalled();

        $container->hasDefinition('app.form.mouse_type')->willReturn(true);
        $container->setDefinition('app.form.mouse_type', $mouseTypeDef)->shouldNotBeCalled();

        $this->process($container);
    }

    /**
     * @param Symfony\Component\DependencyInjection\Definition $cheeseTypeDef
     * @param Symfony\Component\DependencyInjection\Definition $editCheeseTypeDef
     * @param Symfony\Component\DependencyInjection\Definition $mouseTypeDef
     **/
    function it_should_not_register_form_types_if_form_extension_service_is_not_loaded($container, $classFinder, $definitionFactory, $cheeseTypeDef, $editCheeseTypeDef, $mouseTypeDef)
    {
        $container->hasDefinition('form.extension')->willReturn(false);

        $container->setDefinition('app.form.cheese_type', $cheeseTypeDef)->shouldNotBeCalled();
        $container->setDefinition('app.form.edit_cheese_type', $editCheeseTypeDef)->shouldNotBeCalled();
        $container->setDefinition('app.form.mouse_type', $mouseTypeDef)->shouldNotBeCalled();

        $this->process($container);
    }
}
