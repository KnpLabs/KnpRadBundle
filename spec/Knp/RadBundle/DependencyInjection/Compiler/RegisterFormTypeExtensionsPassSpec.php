<?php

namespace spec\Knp\RadBundle\DependencyInjection\Compiler;

use PhpSpec\ObjectBehavior;

class RegisterFormTypeExtensionsPassSpec extends ObjectBehavior
{
    /**
     * @param Symfony\Component\HttpKernel\Bundle\BundleInterface                             $bundle
     * @param Symfony\Component\DependencyInjection\ContainerBuilder                          $container
     * @param Knp\RadBundle\Finder\ClassFinder                                                $classFinder
     * @param Knp\RadBundle\DependencyInjection\Definition\FormTypeExtensionDefinitionFactory $definitionFactory
     * @param Symfony\Component\DependencyInjection\Definition                                $formExtension
     * @param Knp\RadBundle\DependencyInjection\ServiceIdGenerator                            $servIdGen
     */
    function let($bundle, $classFinder, $definitionFactory, $container, $formExtension, $servIdGen)
    {
        $this->beConstructedWith($bundle, $classFinder, $definitionFactory, $servIdGen);
        $container->getParameter('knp_rad.detect.form_extension')->willReturn(true);

        $bundle->getPath()->willReturn('/my/project/src/App');
        $bundle->getNamespace()->willReturn('App');

        $classes = array(
            'App\Form\Extension\CheeseTypeExtension',
            'App\Form\Extension\EditCheeseTypeExtension',
            'App\Form\Extension\MouseTypeExtension',
        );
        $classFinder->findClassesMatching('/my/project/src/App/Form/Extension', 'App\Form\Extension', 'Extension$')->willReturn($classes);
        $classFinder->filterClassesImplementing($classes, 'Symfony\Component\Form\AbstractTypeExtension')->willReturn($classes);

        $servIdGen->generateForBundleClass($bundle, 'App\Form\Extension\CheeseTypeExtension')->willReturn('app.form.extension.cheese_type_extension');
        $servIdGen->generateForBundleClass($bundle, 'App\Form\Extension\EditCheeseTypeExtension')->willReturn('app.form.extension.edit_cheese_type_extension');
        $servIdGen->generateForBundleClass($bundle, 'App\Form\Extension\MouseTypeExtension')->willReturn('app.form.extension.mouse_type_extension');

        $container->getDefinition('form.extension')->willReturn($formExtension);
        $formExtension->getArgument(2)->willReturn(array());
    }

    /**
     * @param Symfony\Component\DependencyInjection\Definition $cheeseTypeExtensionDef
     * @param Symfony\Component\DependencyInjection\Definition $editCheeseTypeExtensionDef
     * @param Symfony\Component\DependencyInjection\Definition $mouseTypeExtensionDef
     **/
    function it_should_add_tagged_service_for_each_form_type(
        $container, $classFinder, $definitionFactory, $formExtension, $cheeseTypeExtensionDef, $editCheeseTypeExtensionDef, $mouseTypeExtensionDef
    )
    {
        $container->hasDefinition('form.extension')->willReturn(true);

        $container->hasDefinition('app.form.extension.cheese_type_extension')->willReturn(false);
        $definitionFactory->createDefinition('App\Form\Extension\CheeseTypeExtension')->willReturn($cheeseTypeExtensionDef);
        $container->setDefinition('app.form.extension.cheese_type_extension', $cheeseTypeExtensionDef)->shouldBeCalled();

        $container->hasDefinition('app.form.extension.edit_cheese_type_extension')->willReturn(false);
        $definitionFactory->createDefinition('App\Form\Extension\EditCheeseTypeExtension')->willReturn($editCheeseTypeExtensionDef);
        $container->setDefinition('app.form.extension.edit_cheese_type_extension', $editCheeseTypeExtensionDef)->shouldBeCalled();

        $container->hasDefinition('app.form.extension.mouse_type_extension')->willReturn(false);
        $definitionFactory->createDefinition('App\Form\Extension\MouseTypeExtension')->willReturn($mouseTypeExtensionDef);
        $container->setDefinition('app.form.extension.mouse_type_extension', $mouseTypeExtensionDef)->shouldBeCalled();

        $formExtension->replaceArgument(2, array(
            'app.form.extension.cheese_type_extension'      => array(0 => 'app.form.extension.cheese_type_extension'),
            'app.form.extension.edit_cheese_type_extension' => array(0 => 'app.form.extension.edit_cheese_type_extension'),
            'app.form.extension.mouse_type_extension'       => array(0 => 'app.form.extension.mouse_type_extension'),
        ))->shouldBeCalled();

        $this->process($container);
    }

    /**
     * @param Symfony\Component\DependencyInjection\Definition $cheeseTypeExtensionDef
     * @param Symfony\Component\DependencyInjection\Definition $editCheeseTypeExtensionDef
     * @param Symfony\Component\DependencyInjection\Definition $mouseTypeExtensionDef
     **/
    function it_should_not_add_service_with_same_id_and_tag_alias(
        $container, $classFinder, $definitionFactory, $formExtension, $cheeseTypeExtensionDef, $editCheeseTypeExtensionDef, $mouseTypeExtensionDef
    )
    {
        $container->hasDefinition('form.extension')->willReturn(true);

        $container->hasDefinition('app.form.extension.cheese_type_extension')->willReturn(false);
        $definitionFactory->createDefinition('App\Form\Extension\CheeseTypeExtension')->willReturn($cheeseTypeExtensionDef);
        $container->setDefinition('app.form.extension.cheese_type_extension', $cheeseTypeExtensionDef)->shouldBeCalled();

        $container->hasDefinition('app.form.extension.edit_cheese_type_extension')->willReturn(true);
        $container->setDefinition('app.form.extension.edit_cheese_type_extension', \Prophecy\Argument::any())->shouldNotBeCalled();

        $container->hasDefinition('app.form.extension.mouse_type_extension')->willReturn(true);
        $container->setDefinition('app.form.extension.mouse_type_extension', \Prophecy\Argument::any())->shouldNotBeCalled();

        $formExtension->replaceArgument(2, array(
            'app.form.extension.cheese_type_extension' => array(0 => 'app.form.extension.cheese_type_extension'),
        ))->shouldBeCalled();

        $this->process($container);
    }

    function it_should_not_register_form_types_if_form_extension_service_is_not_loaded($container)
    {
        $container->hasDefinition('form.extension')->willReturn(false);

        $container->setDefinition('app.form.extension.cheese_type_extension', \Prophecy\Argument::any())->shouldNotBeCalled();
        $container->setDefinition('app.form.extension.edit_cheese_type_extension', \Prophecy\Argument::any())->shouldNotBeCalled();
        $container->setDefinition('app.form.extension.mouse_type_extension', \Prophecy\Argument::any())->shouldNotBeCalled();

        $this->process($container);
    }
}
