<?php

namespace Knp\RadBundle\AppBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConfigurationFactory
{
    public function createConfiguration(ConfigurableBundleInterface $bundle, array $config, ContainerBuilder $container)
    {
        return new Configuration($bundle);
    }
}