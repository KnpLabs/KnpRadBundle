<?php

namespace Knp\RadBundle\AppBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\Config\Definition\Processor;

class ContainerExtension extends Extension
{
    private $bundle;

    public function __construct(ConfigurableBundleInterface $bundle, ConfigurationFactory $configFactory = null, Processor $configProcessor = null)
    {
        $this->bundle = $bundle;
        $this->configFactory = $configFactory ?: new ConfigurationFactory;
        $this->configProcessor = $configProcessor ?: new Processor;
    }

    public function getAlias()
    {
        return strtolower(str_replace('Bundle', '', $this->bundle->getName()));
    }

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $path = $this->bundle->getPath();

        $environment = $container->getParameter('kernel.environment');
        $loader = new Loader\YamlFileLoader(
            $container, new FileLocator($path.'/Resources/config')
        );

        if (file_exists($path.'/Resources/config/services.yml')) {
            $loader->load('services.yml');
        }
        if (file_exists($path.'/Resources/config/services_'.$environment.'.yml')) {
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
            if (is_dir($dirPath = $path.DIRECTORY_SEPARATOR.$dir)) {
                $container->addResource(new DirectoryResource($dirPath));
            }
        }

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->configProcessor->processConfiguration($configuration, $configs);

        $this->bundle->buildContainer($config, $container);
    }

    public function getConfiguration(array $configs, ContainerBuilder $container)
    {
        return $this->configFactory->createConfiguration(
            $this->bundle,
            $configs,
            $container
        );
    }
}
