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

            $collectionRoutes = $this->getCollectionRoutesFromMapping($mapping, $bundle, $class);
            $resourceRoutes   = $this->getResourceRoutesFromMapping($mapping, $bundle, $class);

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

    private function getCollectionRoutesFromMapping($mapping, $bundle, $class)
    {
        $defaults = $this->getDefaultCollectionRoutes($bundle, $class);
        if (!is_array($mapping) || !isset($mapping['collections'])) {
            return $defaults;
        }

        $collections = $mapping['collections'];
        if (0 == count($collections)) {
            return $defaults;
        }

        $routes = array();
        foreach ($collections as $action => $params) {
            if (is_integer($action)) {
                $action = $params;
                $params = null;
            }

            $routeName = $this->getRouteName($bundle, $class, $action);
            if (isset($defaults[$routeName])) {
                $route = $defaults[$routeName];
            } else {
                $route = $this->getCustomCollectionRoute($bundle, $class, $action);
            }

            $this->overrideRouteParams($route, $params);

            $routes[$routeName] = $route;
        }

        return $routes;
    }

    private function getResourceRoutesFromMapping($mapping, $bundle, $class)
    {
        $defaults = $this->getDefaultResourceRoutes($bundle, $class);
        if (!is_array($mapping) || !isset($mapping['resources'])) {
            return $defaults;
        }

        $resources = $mapping['resources'];
        if (0 == count($resources)) {
            return $defaults;
        }

        $routes = array();
        foreach ($resources as $action => $params) {
            if (is_integer($action)) {
                $action = $params;
                $params = null;
            }

            $routeName = $this->getRouteName($bundle, $class, $action);
            if (isset($defaults[$routeName])) {
                $route = $defaults[$routeName];
            } else {
                $route = $this->getCustomResourceRoute($bundle, $class, $action);
            }

            $this->overrideRouteParams($route, $params);

            $routes[$routeName] = $route;
        }

        return $routes;
    }

    private function overrideRouteParams(Route $route, $params)
    {
        if (is_string($params)) {
            $route->setPattern($params);
        }
        if (is_array($params)) {
            if (isset($params['pattern'])) {
                $route->setPattern($params['pattern']);
            }
            if (isset($params['defaults'])) {
                $route->setDefaults(array_merge(
                    $route->getDefaults(), $params['defaults']
                ));
            }
            if (isset($params['requirements'])) {
                $route->setRequirements($params['requirements']);
            }
        }
    }

    private function getDefaultCollectionRoutes($bundle, $class)
    {
        return array(
            $this->getRouteName($bundle, $class, 'index') => new Route(
                '/',
                array('_controller' => sprintf('%s:%s:index', $bundle, $class)),
                array('_method' => 'GET')
            ),
            $this->getRouteName($bundle, $class, 'new') => new Route(
                '/new',
                array('_controller' => sprintf('%s:%s:new', $bundle, $class)),
                array('_method' => 'GET')
            ),
            $this->getRouteName($bundle, $class, 'create') => new Route(
                '/',
                array('_controller' => sprintf('%s:%s:new', $bundle, $class)),
                array('_method' => 'POST')
            ),
        );
    }

    private function getCustomCollectionRoute($bundle, $class, $action)
    {
        return new Route(
            '/'.$action,
            array('_controller' => sprintf('%s:%s:%s', $bundle, $class, $action)),
            array('_method' => 'GET')
        );
    }

    private function getDefaultResourceRoutes($bundle, $class)
    {
        return array(
            $this->getRouteName($bundle, $class, 'show') => new Route(
                '/{id}',
                array('_controller' => sprintf('%s:%s:show', $bundle, $class)),
                array('_method' => 'GET', 'id' => '\\d+')
            ),
            $this->getRouteName($bundle, $class, 'edit') => new Route(
                '/{id}/edit',
                array('_controller' => sprintf('%s:%s:edit', $bundle, $class)),
                array('_method' => 'GET', 'id' => '\\d+')
            ),
            $this->getRouteName($bundle, $class, 'update') => new Route(
                '/{id}',
                array('_controller' => sprintf('%s:%s:edit', $bundle, $class)),
                array('_method' => 'PUT', 'id' => '\\d+')
            ),
            $this->getRouteName($bundle, $class, 'delete') => new Route(
                '/{id}',
                array('_controller' => sprintf('%s:%s:delete', $bundle, $class)),
                array('_method' => 'DELETE', 'id' => '\\d+')
            ),
        );
    }

    private function getCustomResourceRoute($bundle, $class, $action)
    {
        return new Route(
            '/{id}/'.$action,
            array('_controller' => sprintf('%s:%s:%s', $bundle, $class, $action)),
            array('_method' => 'PUT', 'id' => '\\d+')
        );
    }

    private function getRouteName($bundle, $class, $action)
    {
        return sprintf('%s_%s_%s', lcfirst($bundle), lcfirst($class), lcfirst($action));
    }
}
