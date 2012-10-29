<?php

namespace Knp\RadBundle\AppBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class ContainerExtension extends Extension
{
    private $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function getAlias()
    {
        return 'app';
    }

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        if (file_exists($this->path.'/Resources/config/services.yml')) {
            $loader = new Loader\YamlFileLoader(
                $container, new FileLocator($this->path.'/Resources/config')
            );
            $loader->load('services.yml');
        }

        if (file_exists($this->path.'/Resources/config/services.xml')) {
            $loader = new Loader\XmlFileLoader(
                $container, new FileLocator($this->path.'/Resources/config')
            );
            $loader->load('services.xml');
        }
    }
}
