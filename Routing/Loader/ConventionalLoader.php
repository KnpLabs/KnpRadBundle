<?php

namespace Knp\RadBundle\Routing\Loader;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;

class ConventionalLoader implements LoaderInterface
{
    private $locator;
    private $yaml;
    private $resolver;

    public function __construct(FileLocatorInterface $locator, YamlParser $yaml = null)
    {
        $this->locator = $locator;
        $this->yaml    = $yaml ?: new YamlParser;
    }

    public function supports($resource, $type = null)
    {
        return 'rad_convention' === $type;
    }

    public function load($file, $type = null)
    {
        if (null === $this->resolver) {
            throw new \RuntimeException(
                'Please precised a valid resolver before called load'
            );
        }

        $path   = $this->locator->locate($file);
        $routes = $this->yaml->parse($path);

        $collection = new RouteCollection();

        foreach ($routes as $name => $config) {
            $routes = $this->resolver->resolve($config, $name);
            if (!$routes instanceof RouteCollection) {
                throw new \RuntimeException(sprintf(
                    'A route loader for the rad conventional edition must return
                    a RouteCollection. %s::%s has return %s',
                    get_class($this->resolver),
                    'resolve',
                    gettype($routes) === 'object' ?
                        get_class($routes) :
                        gettype($routes)
                ));
            }

            $collection->addCollection($routes);
        }

        return $collection;
    }

    public function setResolver(LoaderResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    public function getResolver()
    {
        return $this->resolver;
    }
}
