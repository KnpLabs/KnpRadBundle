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
        if ($container->hasDefinition('knp_rad.bundle.guesser')) {
            $def = $container->getDefinition('knp_rad.bundle.guesser');
            $bundles = $def->getArgument(2);
            $bundles[] = $this->bundle->getName();
            $def->replaceArgument(2, $bundles);
        }
    }
}
