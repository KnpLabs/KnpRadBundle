<?php

namespace Knp\RadBundle\DependencyInjection\Definition;

use Symfony\Component\DependencyInjection\Definition;

use Knp\RadBundle\Reflection\ReflectionFactory;
use Knp\RadBundle\DependencyInjection\ReferenceFactory;

abstract class AbstractContainerAwareFactory
{
    const CONTAINER_AWARE_INTERFACE = 'Symfony\Component\DependencyInjection\ContainerAwareInterface';
    const CONTAINER_SERVICE_ID = 'service_container';

    private $reflectionFactory;
    private $referenceFactory;

    public function __construct(ReflectionFactory $reflectionFactory = null, ReferenceFactory $referenceFactory = null)
    {
        $this->reflectionFactory = $reflectionFactory ?: new ReflectionFactory();
        $this->referenceFactory  = $referenceFactory ?: new ReferenceFactory();
    }

    protected function injectContainer(Definition $definition)
    {
        $reflClass = $this->reflectionFactory->createReflectionClass($definition->getClass());

        if ($reflClass->implementsInterface(static::CONTAINER_AWARE_INTERFACE)) {
            $containerRef = $this->referenceFactory->createReference(static::CONTAINER_SERVICE_ID);
            $definition->addMethodCall('setContainer', array($containerRef));
        }
    }

    abstract public function createDefinition($className);
}
