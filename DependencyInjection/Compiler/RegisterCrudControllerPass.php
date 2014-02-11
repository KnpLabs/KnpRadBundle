<?php

namespace Knp\RadBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Knp\RadBundle\DependencyInjection\ServiceIdGenerator;
use Knp\RadBundle\DependencyInjection\Definition\CrudControllerFactory;

class RegisterCrudControllerPass implements CompilerPassInterface
{
    public function __construct(BundleInterface $bundle, ServiceIdGenerator $serviceIdGenerator = null, CrudControllerFactory $definitionFactory = null, $className = null)
    {
        $this->bundle = $bundle;
        $this->serviceIdGenerator = $serviceIdGenerator ?: new ServiceIdGenerator;
        $this->definitionFactory = $definitionFactory ?: new CrudControllerFactory;
        $this->className = $className ?: 'Knp\RadBundle\Controller\CrudController';
    }

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $id = $this->serviceIdGenerator->generateForBundleClass($this->bundle, $this->className);

        if ($container->hasDefinition($id)) {
            continue;
        }

        $def = $this->definitionFactory->createDefinition($this->className);
        $container->setDefinition($id, $def);
    }
}
