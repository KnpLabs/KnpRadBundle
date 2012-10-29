<?php

namespace Knp\RadBundle\Routing\Loader;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Config\FileLocatorInterface;

class ConventionalLoader extends FileLoader
{
    private $yaml;

    public function __construct(FileLocatorInterface $locator, YamlParser $yaml = null)
    {
        parent::__construct($locator);

        $this->yaml = $yaml ?: new YamlParser;
    }

    public function supports($resource, $type = null)
    {
        return 'conventional' === $type;
    }

    public function load($file, $type = null)
    {
        $path   = $this->locator->locate($file);
        $config = $this->yaml->parse($path);

        $collection = new RouteCollection();
        $collection->addResource(new FileResource($file));

        foreach ($config as $shortname => $mapping) {
            list($bundle, $class) = explode(':', $shortname, 2);

            $controllerCollection = new RouteCollection();

            $controllerCollection->add(
                sprintf('%s_%s_%s', lcfirst($bundle), lcfirst($class), 'index'),
                new Route('/',
                    array('_controller' => $shortname.':index'),
                    array('_method' => 'GET')
                )
            );

            $controllerCollection->add(
                sprintf('%s_%s_%s', lcfirst($bundle), lcfirst($class), 'new'),
                new Route('/new',
                    array('_controller' => $shortname.':new'),
                    array('_method' => 'GET|POST')
                )
            );

            $controllerCollection->add(
                sprintf('%s_%s_%s', lcfirst($bundle), lcfirst($class), 'show'),
                new Route('/{id}',
                    array('_controller' => $shortname.':show'),
                    array('_method' => 'GET', 'id' => '\\d+')
                )
            );

            $controllerCollection->add(
                sprintf('%s_%s_%s', lcfirst($bundle), lcfirst($class), 'edit'),
                new Route('/{id}/edit',
                    array('_controller' => $shortname.':edit'),
                    array('_method' => 'GET|PUT', 'id' => '\\d+')
                )
            );

            $controllerCollection->add(
                sprintf('%s_%s_%s', lcfirst($bundle), lcfirst($class), 'delete'),
                new Route('/{id}',
                    array('_controller' => $shortname.':delete'),
                    array('_method' => 'DELETE', 'id' => '\\d+')
                )
            );

            $collection->addCollection($controllerCollection, '/'.strtolower($class));
        }

        return $collection;
    }
}
