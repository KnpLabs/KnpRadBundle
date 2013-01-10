<?php

namespace Knp\RadBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Knp\RadBundle\Finder\ClassFinder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\DependencyInjection\Container;
use Knp\RadBundle\DependencyInjection\Definition\DoctrineRepositoryFactory;

class RegisterDoctrineRepositoriesPass implements CompilerPassInterface
{
    private $bundle;
    private $classFinder;
    private $definitionFactory;

    public function __construct(BundleInterface $bundle, ClassFinder $classFinder = null, DoctrineRepositoryFactory $definitionFactory = null)
    {
        $this->bundle = $bundle;
        $this->classFinder = $classFinder ?: new ClassFinder();
        $this->definitionFactory = $definitionFactory ?: new DoctrineRepositoryFactory();
    }

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $directory = $this->bundle->getPath().'/Entity';

        $classes = $this->classFinder->findClassesMatching($directory, 'App\Entity', '(?<!Repository)$');

        foreach ($classes as $class) {
            $baseClass = substr($class, 11);
            $id = sprintf('orm.%s_repository', str_replace('\\', '.', Container::underscore($baseClass)));

            if ($container->hasDefinition($id)) {
                continue;
            }

            $def = $this->definitionFactory->createDefinition($class);

            $container->setDefinition($id, $def);
        }
    }
}
