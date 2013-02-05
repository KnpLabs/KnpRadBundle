<?php

namespace Knp\RadBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Knp\RadBundle\DependencyInjection\Definition\ValidatorConstraintDefinitionFactory;
use Knp\RadBundle\DependencyInjection\ServiceIdGenerator;
use Knp\RadBundle\Finder\ClassFinder;

class RegisterValidatorConstraintsPass implements CompilerPassInterface
{
    public function __construct(BundleInterface $bundle, ClassFinder $classFinder = null, ValidatorConstraintDefinitionFactory $definitionFactory = null, ServiceIdGenerator $serviceIdGenerator = null)
    {
        $this->bundle             = $bundle;
        $this->classFinder        = $classFinder ?: new ClassFinder();
        $this->definitionFactory  = $definitionFactory ?: new ValidatorConstraintDefinitionFactory;
        $this->serviceIdGenerator = $serviceIdGenerator ?: new ServiceIdGenerator;
    }

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('validator.validator_factory')) {
            return;
        }

        $directory = $this->bundle->getPath().'/Validator/Constraints';
        $namespace = $this->bundle->getNamespace().'\\Validator\\Constraints';

        $validators = $container->getDefinition('validator.validator_factory')->getArgument(1);
        $potentialClasses= $this->classFinder->findClassesMatching($directory, $namespace, '(?<!Validator)$');
        $classes = $this->classFinder->filterClassesImplementing($potentialClasses, 'Symfony\Component\Validator\Constraint');

        foreach ($classes as $class) {
            $id = $this->serviceIdGenerator->generateForBundleClass($this->bundle, $class, 'validator');
            $alias = $this->getAlias($class, $id);

            if ($container->hasDefinition($id)) {
                continue;
            }

            $definition = $this->definitionFactory->createDefinition($class.'Validator');
            $container->setDefinition($id, $definition);

            $validators[$alias] = $id;
        }

        $container->getDefinition('validator.validator_factory')->replaceArgument(1, $validators);
    }

    private function getAlias($class, $default)
    {
        if (!class_exists($class)) {
            return $default;
        }

        try {
            return unserialize(sprintf('O:%d:"%s":0:{}', strlen($class), $class))->validatedBy();
        } catch (\Exception $e) {
        }

        return $default;
    }
}
