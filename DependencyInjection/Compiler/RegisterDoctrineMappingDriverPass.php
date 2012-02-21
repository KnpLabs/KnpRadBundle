<?php

namespace Knp\Bundle\RadBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class RegisterDoctrineMappingDriverPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('doctrine')) {
            return;
        }

        foreach ($container->getParameter('doctrine.entity_managers') as $name => $service) {
            $driverId  = sprintf('knp_rad.doctrine.orm.%s_metadata_driver', $name);
            $driverDef = new DefinitionDecorator('knp_rad.doctrine.orm.metadata_driver');
            $container->setDefinition($driverId, $driverDef);

            $driverChainId  = sprintf('doctrine.orm.%s_metadata_driver', $name);
            $driverChainDef = $container->getDefinition($driverChainId);
            $driverChainDef->addMethodCall('addDriver', array(
                new Reference($driverId),
                $container->getParameter('knp_rad.doctrine.entity_namespace')
            ));
        }
    }
}
