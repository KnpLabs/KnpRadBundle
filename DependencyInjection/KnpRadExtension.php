<?php

/*
 * This file is part of the KnpRadBundle package.
 *
 * (c) KnpLabs <http://knplabs.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Knp\Bundle\RadBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Processor;

/**
 * Loads KnpRadBundle configuration.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class KnpRadExtension extends Extension
{
    /**
     * Build the extension services
     *
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor     = new Processor();
        $configuration = new Configuration();
        $config        = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        if ($config['assetic_pipeline']['enabled']) {
            $paths   = $config['assetic_pipeline']['paths'];
            $bundles = $config['assetic_pipeline']['bundles']
                    ?: array_keys($container->getParameter('kernel.bundles'));
            $bundleClasses = $container->getParameter('kernel.bundles');

            foreach ($bundles as $bundle) {
                $bundle = $bundleClasses[$bundle];
                $refl = new \ReflectionClass($bundle);
                if ('Knp\\Bundle\\RadBundle\\HttpKernel\\Bundle\\AppBundle' === $refl->getName()) {
                    continue;
                }
                if ($refl->isSubclassOf('Knp\\Bundle\\RadBundle\\HttpKernel\\Bundle\\AppBundle')) {
                    continue;
                }
                if (file_exists($path = dirname($refl->getFileName()).'/Resources/public')) {
                    $paths[] = $path;
                }
            }
            $container->setParameter('knp_rad.assetic_pipeline.locator.paths', $paths);

            $loader->load('assetic_pipeline.xml');
        }

        if ($config['application_routing']) {
            $loader->load('application_routing.xml');
        }
        if ($config['application_shortaction']) {
            $loader->load('application_shortaction.xml');
        }
        if ($config['application_views']) {
            $loader->load('application_views.xml');
        }

        $loader->load('assetic_coffee_fix.xml');

        $container->setParameter('knp_rad.application_structure', $config['application_structure']);
    }
}
