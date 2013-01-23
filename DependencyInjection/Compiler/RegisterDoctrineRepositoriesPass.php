<?php

namespace Knp\RadBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Knp\RadBundle\Finder\ClassFinder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\DependencyInjection\Container;
use Knp\RadBundle\DependencyInjection\Definition\DoctrineRepositoryFactory;
use Knp\RadBundle\DependencyInjection\ServiceIdGenerator;

class RegisterDoctrineRepositoriesPass implements CompilerPassInterface
{
    private $bundle;
    private $classFinder;
    private $definitionFactory;
    private $serviceIdGenerator;

    public function __construct(BundleInterface $bundle, ClassFinder $classFinder = null, DoctrineRepositoryFactory $definitionFactory = null, ServiceIdGenerator $serviceIdGenerator = null)
    {
        $this->bundle             = $bundle;
        $this->classFinder        = $classFinder ?: new ClassFinder();
        $this->definitionFactory  = $definitionFactory ?: new DoctrineRepositoryFactory();
        $this->serviceIdGenerator = $serviceIdGenerator ?: new ServiceIdGenerator();
    }

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $directory = $this->bundle->getPath().'/Entity';
        $namespace = $this->bundle->getNamespace().'\Entity';

        $classes = $this->classFinder->findClassesMatching($directory, $namespace, '(?<!Repository)$');

        foreach ($classes as $class) {
            $id = $this->serviceIdGenerator->generateForBundleClass($this->bundle, $class, 'repository');

            if ($container->hasDefinition($id)) {
                continue;
            }

            $def = $this->definitionFactory->createDefinition($class);

            $container->setDefinition($id, $def);
        }
    }
}
