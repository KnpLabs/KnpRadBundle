<?php

namespace Knp\RadBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Configuration as FrameworkConfiguration;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KnpRadExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container)
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $bundles = $container->getParameter('kernel.bundles');

        if (!isset($bundles['SecurityBundle'])) {
            $config['security']['enabled'] = false;
        }
        if (!isset($bundles['DoctrineBundle'])) {
            $config['listener']['orm_user'] = false;
        }
        if (!isset($bundles['TwigBundle'])) {
            $config['mailer']['message_factory'] = false;
            $config['flashes']['enabled'] = false;
        }
        if (!isset($bundles['SwiftmailerBundle'])) {
            $config['mailer']['message_factory'] = false;
        }

        $frameworkConfigs = $container->getExtensionConfig($this->getAlias());
        $frameworkConfiguration = new FrameworkConfiguration;
        $frameworkConfig = $this->processConfiguration($frameworkConfiguration, $frameworkConfigs);

        if (!$frameworkConfig['form']['enabled']) {
            $config['form']['enabled'] = false;
        }

        if (!isset($frameworkConfig['templating'])) {
            $config['templating']['enabled'] = false;
        }

        $container->prependExtensionConfig('knp_rad', $config);
    }

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        if ($config['mailer']['logger']) {
            $loader->load('mailer_logger.xml');
        }
        if ($config['mailer']['message_factory']) {
            $loader->load('mailer_message_factory.xml');
        }
        if ($config['templating']['enabled'] && $config['listener']['view']) {
            $loader->load('view_listener.xml');
        }
        if ($config['listener']['resource_resolver']) {
            $loader->load('resource_resolver_listener.xml');
        }
        if ($config['security']['enabled'] && $config['listener']['orm_user']) {
            $loader->load('orm_user_listener.xml');
        }
        if ($config['listener']['exception_rethrow']) {
            $loader->load('exception_rethrow_listener.xml');
        }
        if ($config['routing_loader']) {
            $loader->load('routing_loader.xml');
        }
        if ($config['form']['enabled'] && $config['form']['manager']) {
            $loader->load('form_manager.xml');
        }
        if ($config['security']['enabled'] && $config['security']['voter']) {
            $loader->load('security_voter.xml');
        }
        if ($config['datatable']) {
            $loader->load('datatable.xml');
        }
        $container->setParameter('knp_rad.csrf_link.intention', $config['csrf_links']['intention']);
        if ($config['security']['enabled'] && $config['csrf_links']['enabled']) {
            $loader->load('link_attributes.xml');
        }
        $container->setParameter('knp_rad.flashes.trans_catalog', $config['flashes']['trans_catalog']);
        if ($config['flashes']['enabled']) {
            $loader->load('flashes.xml');
        }
        $container->setParameter('knp_rad.decision_manager.id', $config['security']['decision_manager']);
    }
}
