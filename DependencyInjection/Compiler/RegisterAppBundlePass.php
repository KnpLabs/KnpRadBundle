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
        $bundles = $container->getParameter('kernel.bundles');
        $radBundles = array_filter($bundles, function($bundle) {
            return is_a($bundle, 'Knp\RadBundle\AppBundle\Bundle', true);
        });
        if (count($radBundles) > 1) {
            throw new \LogicException('Only one rad bundle is authorized');
        }

        if ($container->hasDefinition('knp_rad.view.listener')) {
            $viewListenerDef = $container->getDefinition('knp_rad.view.listener');
            $viewListenerDef->addMethodCall('setAppBundleName', array($this->bundle->getName()));
        }

        if ($container->hasDefinition('knp_rad.form.type_creator')) {
            $viewListenerDef = $container->getDefinition('knp_rad.form.type_creator');
            $viewListenerDef->addMethodCall('setAppBundleNamespace', array($this->bundle->getNamespace()));
        }
    }
}

