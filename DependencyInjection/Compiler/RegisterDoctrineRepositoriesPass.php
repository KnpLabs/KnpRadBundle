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
    private $definitionFactory;
    private $serviceIdGenerator;

    public function __construct(BundleInterface $bundle, DoctrineRepositoryFactory $definitionFactory = null, ServiceIdGenerator $serviceIdGenerator = null)
    {
        $this->bundle             = $bundle;
        $this->definitionFactory  = $definitionFactory ?: new DoctrineRepositoryFactory();
        $this->serviceIdGenerator = $serviceIdGenerator ?: new ServiceIdGenerator();
    }

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $namespace = $this->bundle->getNamespace().'\Entity';

        $classes = $container->get('doctrine.orm.entity_manager')->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();
        
        foreach ($classes as $class) {
            if (strpos($class, $this->bundle->getNamespace()) === 0) {
                $id = $this->serviceIdGenerator->generateForBundleClass($this->bundle, $class, 'repository');

                if ($container->hasDefinition($id)) {
                    continue;
                }

                $def = $this->definitionFactory->createDefinition($class);

                $container->setDefinition($id, $def);
            }
        }
    }
}
