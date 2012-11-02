<?php

namespace Knp\RadBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Knp\RadBundle\Finder\ClassFinder;
use Knp\RadBundle\DependencyInjection\Compiler\TwigExtensionServiceFactory;
use Knp\RadBundle\DependencyInjection\ReferenceFactory;

class RegisterTwigExtensionsPass implements CompilerPassInterface
{
    private $bundle;
    private $classFinder;
    private $serviceFactory;

    public function __construct(BundleInterface $bundle, ClassFinder $classFinder = null, TwigExtensionServiceFactory $serviceFactory = null, ReferenceFactory $referenceFactory = null)
    {
        $this->bundle = $bundle;
        $this->classFinder = $classFinder ?: new ClassFinder();
        $this->serviceFactory = $serviceFactory ?: new TwigExtensionServiceFactory();
        $this->referenceFactory = $referenceFactory ?: new ReferenceFactory();
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

        $classes = $this->classFinder->findClassesMatching($directory, $namespace, 'Extension$');

        foreach ($classes as $class) {
            $baseClass = substr($class, strlen($namespace) + 1);

            $id = sprintf('app.twig.%s', str_replace('\\', '.', Container::underscore($baseClass)));
            $def = $this->serviceFactory->createDefinition($class);
            $ref = $this->referenceFactory->createReference($id);

            $container->setDefinition($id, $def);

            $twigDef->addMethodCall('addExtension', array($ref));
        }
    }
}
