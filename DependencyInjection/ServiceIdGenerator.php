<?php

namespace Knp\RadBundle\DependencyInjection;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\DependencyInjection\Container;

class ServiceIdGenerator
{
    public function generateForClassName($className)
    {
        $bundleClass = substr($className, 4);
        $bundlePart  = str_replace('\\', '.', Container::underscore($bundleClass));

        return sprintf('app.%s', $bundlePart);
    }
}
