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

            if (is_string($mapping)) {
                $prefix = $mapping;
            } elseif (is_array($mapping) && isset($mapping['prefix'])) {
                $prefix = $mapping['prefix'];
            } else {
                $prefix = '/'.strtolower($class);
            }

            $defaultCollectionRoutes = $this->getDefaultCollectionRoutes($bundle, $class);
            $defaultResourceRoutes   = $this->getDefaultResourceRoutes($bundle, $class);

            $collectionRoutes = array();
            if (!is_array($mapping) || !isset($mapping['collections'])) {
                $collectionRoutes = $defaultCollectionRoutes;
            }
            $resourceRoutes = array();
            if (!is_array($mapping) || !isset($mapping['resources'])) {
                $resourceRoutes = $defaultResourceRoutes;
            }

            $controllerCollection = new RouteCollection();
            foreach ($collectionRoutes as $name => $route) {
                $controllerCollection->add($name, $route);
            }
            foreach ($resourceRoutes as $name => $route) {
                $controllerCollection->add($name, $route);
            }

            $collection->addCollection($controllerCollection, $prefix);
        }

        return $collection;
    }

    public function getDefaultCollectionRoutes($bundle, $class)
    {
        return array(
            sprintf('%s_%s_index', lcfirst($bundle), lcfirst($class)) => new Route(
                '/',
                array('_controller' => sprintf('%s:%s:index', $bundle, $class)),
                array('_method' => 'GET')
            ),
            sprintf('%s_%s_new', lcfirst($bundle), lcfirst($class)) => new Route(
                '/new',
                array('_controller' => sprintf('%s:%s:new', $bundle, $class)),
                array('_method' => 'GET')
            ),
            sprintf('%s_%s_create', lcfirst($bundle), lcfirst($class)) => new Route(
                '/',
                array('_controller' => sprintf('%s:%s:new', $bundle, $class)),
                array('_method' => 'POST')
            ),
        );
    }

    public function getDefaultResourceRoutes($bundle, $class)
    {
        return array(
            sprintf('%s_%s_show', lcfirst($bundle), lcfirst($class)) => new Route(
                '/{id}',
                array('_controller' => sprintf('%s:%s:show', $bundle, $class)),
                array('_method' => 'GET', 'id' => '\\d+')
            ),
            sprintf('%s_%s_edit', lcfirst($bundle), lcfirst($class)) => new Route(
                '/{id}/edit',
                array('_controller' => sprintf('%s:%s:edit', $bundle, $class)),
                array('_method' => 'GET', 'id' => '\\d+')
            ),
            sprintf('%s_%s_update', lcfirst($bundle), lcfirst($class)) => new Route(
                '/{id}',
                array('_controller' => sprintf('%s:%s:edit', $bundle, $class)),
                array('_method' => 'PUT', 'id' => '\\d+')
            ),
            sprintf('%s_%s_delete', lcfirst($bundle), lcfirst($class)) => new Route(
                '/{id}',
                array('_controller' => sprintf('%s:%s:delete', $bundle, $class)),
                array('_method' => 'DELETE', 'id' => '\\d+')
            ),
        );
    }
}
