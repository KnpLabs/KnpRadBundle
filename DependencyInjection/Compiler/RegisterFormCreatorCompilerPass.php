<?php

namespace Knp\RadBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterFormCreatorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('knp_rad.form.manager')) {
            return;
        }

        $definition = $container->getDefinition(
            'knp_rad.form.manager'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'knp_rad.form.creator'
        );

        foreach ($taggedServices as $id => $attribute) {
            $definition->addMethodCall(
                'registerCreator',
                array(new Reference($id))
            );
        }
    }
}
