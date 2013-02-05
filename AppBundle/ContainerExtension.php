<?php

namespace Knp\RadBundle\AppBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\Resource\DirectoryResource;

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
        $environment = $container->getParameter('kernel.environment');
        $loader = new Loader\YamlFileLoader(
            $container, new FileLocator($this->path.'/Resources/config')
        );

        if (file_exists($this->path.'/Resources/config/services.yml')) {
            $loader->load('services.yml');
        }
        if (file_exists($this->path.'/Resources/config/services_'.$environment.'.yml')) {
            $loader->load('services_'.$environment.'.yml');
        }

        $paths = array(
            'Entity',
            'Form',
            'Security',
            'Twig',
            'Validator'.DIRECTORY_SEPARATOR.'Constraints',
            'Resources'.DIRECTORY_SEPARATOR.'config',
            'Resources'.DIRECTORY_SEPARATOR.'translations',
        );

        foreach ($paths as $dir) {
            if (is_dir($dirPath = $this->path.DIRECTORY_SEPARATOR.$dir)) {
                $container->addResource(new DirectoryResource($dirPath));
            }
        }
    }
}
