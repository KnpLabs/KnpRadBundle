<?php

namespace Knp\RadBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\Kernel;

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

        $loader->load('bundle.xml');
        $loader->load('controller_helper.xml');

        foreach ($config['detect'] as $type => $isActivated) {
            $container->setParameter('knp_rad.detect.'.$type, $isActivated);
        }

        if ($config['domain_event']) {
            $loader->load('domain_event.xml');
        }
        if ($config['mailer']['logger']) {
            $loader->load('mailer_logger.xml');
        }
        if ($config['mailer']['message_factory']) {
            $loader->load('mailer_message_factory.xml');
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
        if ($config['listener']['exception_rethrow']) {
            $loader->load('exception_rethrow_listener.xml');
        }
        if ($config['routing_loader']) {
            $loader->load('routing_loader.xml');
        }
        if ($config['form_manager']) {
            $loader->load('form_manager.xml');
        }
        if ($config['security_voter']) {
            $loader->load('security_voter.xml');
        }
        if ($config['datatable']) {
            $loader->load('datatable.xml');
        }
        if ($config['alice']) {
            $loader->load('alice.xml');
        }
        $container->setParameter('knp_rad.csrf_link.intention', $config['csrf_links']['intention']);
        if ($this->isConfigEnabled($container, $config['csrf_links'])) {
            $loader->load('link_attributes.xml');
        }
        $container->setParameter('knp_rad.flashes.trans_catalog', $config['flashes']['trans_catalog']);
        if ($this->isConfigEnabled($container, $config['flashes'])) {
            $loader->load('flashes.xml');
        }
        $container->setParameter('knp_rad.decision_manager.id', $config['security']['decision_manager']);

        $container->setAlias('knp_rad.resource.resolver.resource', 'knp_rad.resource.resolver.resource.aggregate');
    }

    public function getNamespace()
    {
        return 'http://knplabs.com/schema/dic/rad';
    }
}
