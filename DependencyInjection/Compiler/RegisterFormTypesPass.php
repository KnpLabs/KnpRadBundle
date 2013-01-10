<?php

namespace Knp\RadBundle\DependencyInjection\Compiler;

namespace Knp\RadBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Knp\RadBundle\Finder\ClassFinder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Definition;
use Knp\RadBundle\DependencyInjection\Definition\FormTypeDefinitionFactory;
use Knp\RadBundle\DependencyInjection\ServiceIdGenerator;

class RegisterFormTypesCompilerPass implements CompilerPassInterface
{
    private $bundle;
    private $classFinder;
    private $definitionFactory;
    private $serviceIdGenerator;

    public function __construct(BundleInterface $bundle, ClassFinder $classFinder = null, FormTypeDefinitionFactory $definitionFactory = null, ServiceIdGenerator $serviceIdGenerator = null)
    {
        $this->bundle = $bundle;
        $this->classFinder = $classFinder ?: new ClassFinder();
        $this->definitionFactory = $definitionFactory ?: new FormTypeDefinitionFactory;
        $this->serviceIdGenerator = $serviceIdGenerator ?: new ServiceIdGenerator;
    }
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $directory = $this->bundle->getPath().'/Form';
        $namespace = $this->bundle->getNamespace().'\\Form';

        $types   = $container->getDefinition('form.extension')->getArgument(1);
        $classes = $this->classFinder->findClassesMatching($directory, $namespace, 'Type$');

        foreach ($classes as $class) {
            $id = $this->serviceIdGenerator->generateForBundleClass($this->bundle, $class);
            $alias = $this->getAlias($class, $id);

            if ($container->hasDefinition($id)) {
                continue;
            }

            $definition = $this->definitionFactory->createDefinition($class);
            $container->setDefinition($id, $definition);

            $types[$alias] = $id;
        }

        $container->getDefinition('form.extension')->replaceArgument(1, $types);
    }

    private function getAlias($class, $default)
    {
        if (!class_exists($class)) {
            return $default;
        }

        try {
            return unserialize(sprintf('O:%d:"%s":0:{}', strlen($class), $class))->getName();
        } catch (\Exception $e) {
        }

        return $default;
    }
}
