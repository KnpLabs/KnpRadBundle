<?php

namespace Knp\RadBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RegisterDoctrineDefaultRepositoryPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasAlias('doctrine.orm.entity_manager')) {

            $ormDefaultConfName = substr_replace($container->getAlias('doctrine.orm.entity_manager'), 'configuration', -14);

            if ($container->hasDefinition($ormDefaultConfName)) {
                $ormDefaultConf = $container->getDefinition($ormDefaultConfName);
                if ($ormDefaultConf->hasMethodCall('setDefaultRepositoryClassName')) {
                    $calls = $ormDefaultConf->getMethodCalls();
                    foreach ($calls as $i => $call) {
                        if ($call[0] === 'setDefaultRepositoryClassName') {
                            if ($call[1] == array('Doctrine\ORM\EntityRepository')) {
                                $ormDefaultConf->addMethodCall('setDefaultRepositoryClassName', array('Knp\RadBundle\Doctrine\EntityRepository'));
                            }
                            break;
                        }
                    }
                }
            }

        }
    }
}
