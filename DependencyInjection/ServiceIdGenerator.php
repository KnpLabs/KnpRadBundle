<?php

namespace Knp\RadBundle\DependencyInjection;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\DependencyInjection\Container;

class ServiceIdGenerator
{
    public function generateForBundleClass(BundleInterface $bundle, $className)
    {
        $namespace = $bundle->getNamespace();
        $extension = $bundle->getExtension();

        $extensionAlias = $extension->getAlias();

        $bundleClass = substr($className, strlen($namespace) + 1);
        $bundlePart = str_replace('\\', '.', Container::underscore($bundleClass));

        return sprintf('%s.%s', $extensionAlias, $bundlePart);
    }
}
