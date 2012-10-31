<?php

namespace Knp\RadBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Knp\RadBundle\Finder\ClassFinder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\DependencyInjection\Container;

class RegisterDoctrineRepositoriesPass implements CompilerPassInterface
{
    private $bundle;
    private $classFinder;
    private $definitionFactory;

    public function __construct(BundleInterface $bundle, ClassFinder $classFinder = null, RepositoryDefinitionFactory $definitionFactory = null)
    {
        $this->bundle = $bundle;
        $this->classFinder = $classFinder ?: new ClassFinder();
        $this->definitionFactory = $definitionFactory ?: new RepositoryDefinitionFactory();
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
            $baseClass = substr($class, strlen($namespace) + 1);

            $id = sprintf('orm.%s_repository', str_replace('\\', '.', Container::underscore($baseClass)));
            $def = $this->definitionFactory->createDefinition($class);

            $container->setDefinition($id, $def);
        }
    }
}
