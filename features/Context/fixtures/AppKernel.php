<?php

namespace fixtures;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

require __DIR__.'/App.php';

class AppKernel extends Kernel
{
    public function __construct()
    {
        $this->name = 'app'.uniqid();
        parent::__construct('test', true);
    }

    public function registerBundles()
    {
        return array(
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle,
            new \Symfony\Bundle\TwigBundle\TwigBundle,
            new \Knp\RadBundle\KnpRadBundle,
            new \fixtures\App,
        );
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function($container) {
            $container->loadFromExtension('framework', array(
                'test' => null,
                'session' => array(
                    'storage_id' => 'session.storage.mock_file',
                ),
                'secret' => '%kernel.secret%',
                'csrf_protection' => null,
                'form' => null,
                'router' => array('resource' => __DIR__.'/routing.yml'),
                'validation' => array('enable_annotations' => true),
                'templating' => array(
                    'engines' => array('twig'),
                ),
            ));
            $container->loadFromExtension('knp_rad', array(
                'csrf_links' => array('enabled' => true,),
            ));
        });
    }

    protected function getKernelParameters()
    {
        $parameters = parent::getKernelParameters();
        $parameters['kernel.secret'] = 'secret!';

        return $parameters;
    }

    public function getCacheDir()
    {
        return $this->rootDir.'/tmp/cache/'.$this->name.$this->environment;
    }

    public function getLogDir()
    {
        return $this->rootDir.'/tmp/logs';
    }
}
