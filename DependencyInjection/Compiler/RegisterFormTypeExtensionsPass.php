<?php

namespace Knp\RadBundle\DependencyInjection\Compiler;

namespace Knp\RadBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Knp\RadBundle\DependencyInjection\Definition\FormTypeExtensionDefinitionFactory;
use Knp\RadBundle\DependencyInjection\ServiceIdGenerator;
use Knp\RadBundle\Finder\ClassFinder;

class RegisterFormTypeExtensionsPass implements CompilerPassInterface
{
    private $bundle;
    private $classFinder;
    private $definitionFactory;
    private $serviceIdGenerator;

    public function __construct(BundleInterface $bundle, ClassFinder $classFinder = null, FormTypeExtensionDefinitionFactory $definitionFactory = null, ServiceIdGenerator $serviceIdGenerator = null)
    {
        $this->bundle = $bundle;
        $this->classFinder = $classFinder ?: new ClassFinder();
        $this->definitionFactory = $definitionFactory ?: new FormTypeExtensionDefinitionFactory;
        $this->serviceIdGenerator = $serviceIdGenerator ?: new ServiceIdGenerator;
    }

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('form.extension')) {
            return;
        }

        $directory = $this->bundle->getPath().'/Form/Extension';
        $namespace = $this->bundle->getNamespace().'\\Form\\Extension';

        $typeExtensions   = $container->getDefinition('form.extension')->getArgument(2);
        $potentialClasses = $this->classFinder->findClassesMatching($directory, $namespace, 'Extension$');
        $classes = $this->classFinder->filterClassesSubclassing($potentialClasses, 'Symfony\Component\Form\AbstractTypeExtension');

        foreach ($classes as $class) {
            $id = $this->serviceIdGenerator->generateForBundleClass($this->bundle, $class);
            $alias = $this->getAlias($class, $id);

            if ($container->hasDefinition($id)) {
                continue;
            }

            $definition = $this->definitionFactory->createDefinition($class);
            $container->setDefinition($id, $definition);

            $typeExtensions[$alias][] = $id;
        }

        $container->getDefinition('form.extension')->replaceArgument(2, $typeExtensions);
    }

    private function getAlias($class, $default)
    {
        if (!class_exists($class)) {
            return $default;
        }

        try {
            return unserialize(sprintf('O:%d:"%s":0:{}', strlen($class), $class))->getExtendedType();
        } catch (\Exception $e) {
        }

        return $default;
    }
}
