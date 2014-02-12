<?php

namespace spec\Knp\RadBundle\DependencyInjection\Compiler;

use PhpSpec\ObjectBehavior;

class RegisterTwigExtensionsPassSpec extends ObjectBehavior
{
    /**
     * @param Symfony\Component\HttpKernel\Bundle\BundleInterface $bundle
     * @param Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param Knp\RadBundle\Finder\ClassFinder $classFinder
     * @param Knp\RadBundle\DependencyInjection\Definition\TwigExtensionFactory $definitionFactory
     * @param Knp\RadBundle\DependencyInjection\ReferenceFactory $referenceFactory
     * @param Knp\RadBundle\DependencyInjection\ServiceIdGenerator $serviceIdGenerator
     * @param Symfony\Component\DependencyInjection\Definition $twigDef
     */
    function let($bundle, $container, $classFinder, $definitionFactory, $referenceFactory, $serviceIdGenerator, $twigDef)
    {
        $container->getParameter('knp_rad.detect.twig')->willReturn(true);
        $this->beConstructedWith($bundle, $classFinder, $definitionFactory, $referenceFactory, $serviceIdGenerator);

        $bundle->getPath()->willReturn('/my/project/src/App');
        $bundle->getNamespace()->willReturn('App');
    }

    function it_should_be_a_compiler_pass()
    {
        $this->shouldBeAnInstanceOf('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface');
    }

    /**
     * @param Symfony\Component\DependencyInjection\Definition $breadDef
     * @param Symfony\Component\DependencyInjection\Definition $wineDef
     * @param Symfony\Component\DependencyInjection\Reference $breadRef
     * @param Symfony\Component\DependencyInjection\Reference $wineRef
     */
    function it_should_register_all_twig_extensions_found_in_the_bundle($bundle, $container, $classFinder, $definitionFactory, $referenceFactory, $serviceIdGenerator, $twigDef, $breadDef, $wineDef, $breadRef, $wineRef)
    {
        $container->hasDefinition('twig')->willReturn(true);
        $container->getDefinition('twig')->willReturn($twigDef);

        $classes = array(
            'App\Twig\BreadExtension',
            'App\Twig\WineExtension'
        );
        $classFinder->findClassesMatching('/my/project/src/App/Twig', 'App\Twig', 'Extension$')->willReturn($classes);
        $classFinder->filterClassesImplementing($classes, 'Twig_ExtensionInterface')->willReturn($classes);

        $container->hasDefinition('app.twig.bread_extension')->willReturn(false)->shouldBeCalled();
        $container->hasDefinition('app.twig.wine_extension')->willReturn(false)->shouldBeCalled();

        $definitionFactory->createDefinition('App\Twig\BreadExtension')->shouldBeCalled()->willReturn($breadDef);
        $definitionFactory->createDefinition('App\Twig\WineExtension')->shouldBeCalled()->willReturn($wineDef);

        $serviceIdGenerator->generateForBundleClass($bundle, 'App\Twig\BreadExtension')->shouldBeCalled()->willReturn('app.twig.bread_extension');
        $serviceIdGenerator->generateForBundleClass($bundle, 'App\Twig\WineExtension')->shouldBeCalled()->willReturn('app.twig.wine_extension');

        $container->setDefinition('app.twig.bread_extension', $breadDef)->shouldBeCalled();
        $container->setDefinition('app.twig.wine_extension', $wineDef)->shouldBeCalled();

        $referenceFactory->createReference('app.twig.bread_extension')->shouldBeCalled()->willReturn($breadRef);
        $referenceFactory->createReference('app.twig.wine_extension')->shouldBeCalled()->willReturn($wineRef);

        $twigDef->addMethodCall('addExtension', array($breadRef->getWrappedObject()))->shouldBeCalled();
        $twigDef->addMethodCall('addExtension', array($wineRef->getWrappedObject()))->shouldBeCalled();

        $this->process($container);
    }

    /**
     * @param Symfony\Component\DependencyInjection\Definition $breadDef
     * @param Symfony\Component\DependencyInjection\Definition $wineDef
     * @param Symfony\Component\DependencyInjection\Reference $breadRef
     * @param Symfony\Component\DependencyInjection\Reference $wineRef
     */
    function it_should_not_register_already_defined_services($bundle, $container, $classFinder, $definitionFactory, $referenceFactory, $serviceIdGenerator, $twigDef, $breadDef, $wineDef, $breadRef, $wineRef)
    {
        $container->hasDefinition('twig')->willReturn(true);
        $container->getDefinition('twig')->willReturn($twigDef);

        $classes = array(
            'App\Twig\BreadExtension',
            'App\Twig\WineExtension'
        );
        $classFinder->findClassesMatching('/my/project/src/App/Twig', 'App\Twig', 'Extension$')->willReturn($classes);
        $classFinder->filterClassesImplementing($classes, 'Twig_ExtensionInterface')->willReturn($classes);

        $container->hasDefinition('app.twig.bread_extension')->willReturn(true)->shouldBeCalled();
        $container->hasDefinition('app.twig.wine_extension')->willReturn(false)->shouldBeCalled();

        $definitionFactory->createDefinition('App\Twig\BreadExtension')->shouldNotBeCalled();
        $definitionFactory->createDefinition('App\Twig\WineExtension')->shouldBeCalled()->willReturn($wineDef);

        $serviceIdGenerator->generateForBundleClass($bundle, 'App\Twig\BreadExtension')->shouldBeCalled()->willReturn('app.twig.bread_extension');
        $serviceIdGenerator->generateForBundleClass($bundle, 'App\Twig\WineExtension')->shouldBeCalled()->willReturn('app.twig.wine_extension');

        $container->setDefinition('app.twig.bread_extension', $breadDef)->shouldNotBeCalled();
        $container->setDefinition('app.twig.wine_extension', $wineDef)->shouldBeCalled();

        $referenceFactory->createReference('app.twig.bread_extension')->shouldNotBeCalled();
        $referenceFactory->createReference('app.twig.wine_extension')->shouldBeCalled()->willReturn($wineRef);

        $twigDef->addMethodCall('addExtension', array($breadRef->getWrappedObject()))->shouldNotBeCalled();
        $twigDef->addMethodCall('addExtension', array($wineRef->getWrappedObject()))->shouldBeCalled();

        $this->process($container);
    }

    function it_should_abort_processing_when_twig_is_not_defined($container, $classFinder)
    {
        $container->hasDefinition('twig')->willReturn(false);
        $container->getDefinition('twig')->shouldNotBeCalled();
        $classFinder->findClassesMatching(\Prophecy\Argument::cetera())->shouldNotBeCalled();

        $this->process($container);
    }
 }
