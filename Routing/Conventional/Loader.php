<?php

namespace Knp\RadBundle\Routing\Conventional;

use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Routing\RouteCollection;
use Knp\RadBundle\Routing\Conventional\Factory;
use Knp\RadBundle\Routing\Conventional\Config\Factory as Configs;

class Loader extends FileLoader
{
    private $configs;
    private $factory;

    public function __construct(FileLocatorInterface $locator, Configs $configs = null, Factory $factory = null)
    {
        parent::__construct($locator);
        $this->configs = $configs ?: new Configs;
        $this->factory = $factory ?: new Factory;
    }

    public function supports($resource, $type = null)
    {
        return 'rad' === $type;
    }

    public function load($file, $type = null)
    {
        $path   = $this->locator->locate($file);
        $collection = new RouteCollection;
        $collection->addResource(new FileResource($file));

        foreach ($this->configs->all($path) as $config) {
            foreach ($config->getElements() as $element) {
                list ($name, $route) = $this->factory->create($element);
                $collection->add($name, $route);
            }
        }

        return $collection;
    }
}
