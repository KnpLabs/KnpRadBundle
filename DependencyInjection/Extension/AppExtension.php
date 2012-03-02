<?php

/*
 * This file is part of the KnpRadBundle package.
 *
 * (c) KnpLabs <http://knplabs.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Knp\Bundle\RadBundle\DependencyInjection\Extension;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Finder\Finder;

/**
 * Application extension automatically loads service
 * definitions from application bundle.
 */
class AppExtension extends Extension
{
    private $path;

    /**
     * Initializes extension.
     *
     * @param string $path Extension path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * Build the extension services
     *
     * @param array            $configs   Merged configuration array
     * @param ContainerBuilder $container Container builder
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $ymlLoader = $this->getYamlFileLoader($container);

        if (file_exists($this->path.'/Resources/config/services.yml')) {
            $ymlLoader->load('services.yml');
        }

        foreach ($configs as $config) {
            foreach ($config as $key => $val) {
                $container->setParameter($this->getAlias().'.'.$key, $val);
            }
        }
    }

    /**
     * Returns extension alias (configuration name).
     *
     * @return string
     */
    public function getAlias()
    {
        return 'app';
    }

    /**
     * Returns new container YamlFileLoader.
     *
     * @return YamlFileLoader
     */
    protected function getYamlFileLoader(ContainerBuilder $container)
    {
        return new YamlFileLoader($container, new FileLocator($this->path.'/Resources/config'));
    }
}
