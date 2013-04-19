<?php

namespace Knp\RadBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Knp\RadBundle\Finder\ClassFinder;
use Knp\RadBundle\DependencyInjection\Definition\TwigExtensionFactory;
use Knp\RadBundle\DependencyInjection\ReferenceFactory;
use Knp\RadBundle\DependencyInjection\ServiceIdGenerator;

class RegisterTwigExtensionsPass implements CompilerPassInterface
{
    private $bundle;
    private $classFinder;
    private $definitionFactory;
    private $serviceIdGenerator;

    public function __construct(BundleInterface $bundle, ClassFinder $classFinder = null, TwigExtensionFactory $definitionFactory = null, ReferenceFactory $referenceFactory = null, ServiceIdGenerator $serviceIdGenerator = null)
    {
        $this->bundle = $bundle;
        $this->classFinder = $classFinder ?: new ClassFinder();
        $this->definitionFactory = $definitionFactory ?: new TwigExtensionFactory();
        $this->referenceFactory = $referenceFactory ?: new ReferenceFactory();
        $this->serviceIdGenerator = $serviceIdGenerator ?: new ServiceIdGenerator();
    }

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('twig')) {
            return;
        }

        $twigDef = $container->getDefinition('twig');

        $directory = $this->bundle->getPath().'/Twig';
        $namespace = $this->bundle->getNamespace().'\Twig';

        $potentialClasses = $this->classFinder->findClassesMatching($directory, $namespace, 'Extension$');
        $classes = $this->classFinder->filterClassesSubclassing($potentialClasses, 'Twig_ExtensionInterface');

        foreach ($classes as $class) {
            $id = $this->serviceIdGenerator->generateForBundleClass($this->bundle, $class);

            if ($container->hasDefinition($id)) {
                continue;
            }

            $def = $this->definitionFactory->createDefinition($class);
            $ref = $this->referenceFactory->createReference($id);

            $container->setDefinition($id, $def);

            $twigDef->addMethodCall('addExtension', array($ref));
        }
    }
}
