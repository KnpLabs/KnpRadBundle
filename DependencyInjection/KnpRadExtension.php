<?php

namespace Knp\RadBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KnpRadExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        if ($config['mailer_logger']) {
            $loader->load('mailer_logger.xml');
        }

        if ($config['listener']['view']) {
            $loader->load('view_listener.xml');
        }
        if ($config['listener']['resource_resolver']) {
            $loader->load('resource_resolver_listener.xml');
        }
        if ($config['listener']['orm_user']) {
            $loader->load('orm_user_listener.xml');
        }

        if ($config['routing_loader']) {
            $loader->load('routing_loader.xml');
        }

        if ($config['form_manager']) {
            $loader->load('form_manager.xml');
        }

        if ($config['datatable']) {
            $loader->load('datatable.xml');
        }

        $container->setParameter('knp_rad.flashes.trans_catalog', $config['flashes']['trans_catalog']);
        if ($config['flashes']['enabled']) {
            $loader->load('flashes.xml');
        }
    }
}
