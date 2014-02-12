<?php

namespace Knp\RadBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Knp\RadBundle\DependencyInjection\ReferenceFactory;

class RegisterFormCreatorsPass implements CompilerPassInterface
{
    public function __construct(ReferenceFactory $referenceFactory = null)
    {
        $this->referenceFactory = $referenceFactory ?: new ReferenceFactory();
    }

    public function process(ContainerBuilder $container)
    {
        if (!$container->getParameter('knp_rad.detect.form_creator')) {
            return;
        }

        if (!$container->hasDefinition('knp_rad.form.manager')) {
            return;
        }

        $definition = $container->getDefinition(
            'knp_rad.form.manager'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'knp_rad.form.creator'
        );

        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall(
                'registerCreator',
                array($this->referenceFactory->createReference($id), $this->getPriority($attributes))
            );
        }
    }

    private function getPriority(array $attributes = array())
    {
        if (isset($attributes[0]['priority'])) {
            return $attributes[0]['priority'];
        }

        return 0;
    }
}
