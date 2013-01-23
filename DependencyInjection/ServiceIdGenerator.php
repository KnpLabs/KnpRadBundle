<?php

namespace Knp\RadBundle\DependencyInjection;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\DependencyInjection\Container;

class ServiceIdGenerator
{
    public function generateForBundleClass(BundleInterface $bundle, $className, $withSuffix = false)
    {
        $namespace = $bundle->getNamespace();
        $extension = $bundle->getContainerExtension();

        $extensionAlias = $extension->getAlias();

        $bundleClass = substr($className, strlen($namespace) + 1);
        $bundlePart = str_replace('\\', '.', Container::underscore($bundleClass));

        if (false !== $withSuffix) {
            $bundlePart .= '_'.$withSuffix;
        }

        return sprintf('%s.%s', $extensionAlias, $bundlePart);
    }
}
