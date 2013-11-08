<?php

namespace App;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        return array(
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle,
            new \Symfony\Bundle\TwigBundle\TwigBundle,
            new \Knp\RadBundle\KnpRadBundle,
            new \App\App,
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
                'form' => null,
                'router' => array('resource' => __DIR__.'/routing.yml'),
                'validation' => array('enable_annotations' => true),
                'templating' => array(
                    'engines' => array('twig'),
                ),
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
        return $this->rootDir.'/../cache/'.$this->environment;
    }

    public function getLogDir()
    {
        return $this->rootDir.'/../logs';
    }
}
