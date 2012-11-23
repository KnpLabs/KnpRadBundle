<?php

namespace Knp\RadBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RegisterAppBundlePass implements CompilerPassInterface
{
    public function __construct(BundleInterface $bundle)
    {
        $this->bundle = $bundle;
    }

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('knp_rad.view.listener')) {
            return;
        }

        $viewListenerDef = $container->getDefinition('knp_rad.view.listener');
        $viewListenerDef->addMethodCall('setAppBundleName', array($this->bundle->getName()));
    }
}

