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
            } elseif (isset($mapping['collections'])) {
                foreach ($mapping['collections'] as $action => $params) {
                    if (is_integer($action)) {
                        $action = $params;
                        $params = null;
                    }

                    $routeName = $this->getRouteName($bundle, $class, $action);
                    $route = isset($defaultCollectionRoutes[$routeName])
                        ? $defaultCollectionRoutes[$routeName]
                        : $this->getCustomCollectionRoute($bundle, $class, $action);

                    if (is_string($params)) {
                        $route->setPattern($params);
                    }

                    $collectionRoutes[$routeName] = $route;
                }
            }

            $resourceRoutes = array();
            if (!is_array($mapping) || !isset($mapping['resources'])) {
                $resourceRoutes = $defaultResourceRoutes;
            } elseif (isset($mapping['resources'])) {
                foreach ($mapping['resources'] as $action => $params) {
                    if (is_integer($action)) {
                        $action = $params;
                        $params = null;
                    }

                    $routeName = $this->getRouteName($bundle, $class, $action);
                    $route = isset($defaultResourceRoutes[$routeName])
                        ? $defaultResourceRoutes[$routeName]
                        : $this->getCustomResourceRoute($bundle, $class, $action);

                    if (is_string($params)) {
                        $route->setPattern($params);
                    }

                    $resourceRoutes[$routeName] = $route;
                }
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
