<?php

namespace Knp\RadBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Knp\RadBundle\Finder\ClassFinder;
use Knp\RadBundle\DependencyInjection\Definition\SecurityVoterFactory;
use Knp\RadBundle\DependencyInjection\ReferenceFactory;
use Knp\RadBundle\DependencyInjection\ServiceIdGenerator;
use Knp\RadBundle\DependencyInjection\DefinitionManipulator;

class RegisterSecurityVotersPass implements CompilerPassInterface
{
    private $bundle;
    private $classFinder;
    private $definitionFactory;
    private $referenceFactory;
    private $serviceIdGenerator;
    private $definitionManipulator;

    public function __construct(BundleInterface $bundle, ClassFinder $classFinder = null, SecurityVoterFactory $definitionFactory = null, ReferenceFactory $referenceFactory = null, ServiceIdGenerator $serviceIdGenerator = null, DefinitionManipulator $definitionManipulator = null)
    {
        $this->bundle = $bundle;
        $this->classFinder = $classFinder ?: new ClassFinder();
        $this->definitionFactory = $definitionFactory ?: new SecurityVoterFactory();
        $this->referenceFactory = $referenceFactory ?: new ReferenceFactory();
        $this->serviceIdGenerator = $serviceIdGenerator ?: new ServiceIdGenerator();
        $this->definitionManipulator = $definitionManipulator ?: new DefinitionManipulator();
    }

    public function process(ContainerBuilder $container)
    {
        $decisionManagerId = $container->getParameter('knp_rad.decision_manager.id');

        if (false === $container->hasDefinition($decisionManagerId)) {
            return;
        }

        $decisionManagerDef = $container->getDefinition($decisionManagerId);

        $directory = $this->bundle->getPath().'/Security';
        $namespace = $this->bundle->getNamespace().'\Security';

        $potentialClasses = $this->classFinder->findClassesMatching($directory, $namespace, 'Voter$');
        $classes = $this->classFinder->filterClassesSubclassing($potentialClasses, 'Symfony\Component\Security\Core\Authorization\Voter\VoterInterface');

        foreach ($classes as $class) {
            $id = $this->serviceIdGenerator->generateForBundleClass($this->bundle, $class);

            if ($container->hasDefinition($id)) {
                continue;
            }

            $def = $this->definitionFactory->createDefinition($class);
            $ref = $this->referenceFactory->createReference($id);

            $container->setDefinition($id, $def);

            $this->definitionManipulator->appendArgumentValue($decisionManagerDef, 0, $ref);
        }
    }
}
