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
        $bundles    = $container->getParameter('kernel.bundles');
        $radBundles = array_filter($bundles, function($bundle) {
            return 'Knp\RadBundle\AppBundle\Bundle' === $bundle
                || is_subclass_of($bundle, 'Knp\RadBundle\AppBundle\Bundle');
        });

        if (count($radBundles) > 1) {
            throw new \LogicException('Only one rad bundle is authorized.');
        }

        if ($container->hasDefinition('knp_rad.view.listener')) {
            $viewListenerDef = $container->getDefinition('knp_rad.view.listener');
            $viewListenerDef->replaceArgument(3, $this->bundle->getName());
        }

        if ($container->hasDefinition('knp_rad.form.type_creator')) {
            $typeCreatorDef = $container->getDefinition('knp_rad.form.type_creator');
            $typeCreatorDef->replaceArgument(3, $this->bundle->getNamespace());
        }

        if ($container->hasDefinition('knp_rad.mailer.message_factory')) {
            $messageFactoryDef = $container->getDefinition('knp_rad.mailer.message_factory');
            $messageFactoryDef->replaceArgument(2, $this->bundle->getName());
        }
    }
}

