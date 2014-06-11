<?php

namespace Knp\RadBundle\Routing\Loader;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Config\FileLocatorInterface;

class ConventionalLoader extends YamlFileLoader
{
    private static $supportedControllerKeys = array(
        'prefix', 'defaults', 'requirements', 'options', 'collections', 'resources'
    );
    private static $supportedActionKeys = array(
        'pattern', 'defaults', 'requirements', 'options'
    );
    private $yaml;

    public function __construct(FileLocatorInterface $locator, YamlParser $yaml = null)
    {
        parent::__construct($locator);

        $this->yaml = $yaml ?: new YamlParser;
    }

    public function supports($resource, $type = null)
    {
        return 'rad_convention' === $type;
    }

    public function load($file, $type = null)
    {
        $path   = $this->locator->locate($file);
        $config = $this->yaml->parse($path);

        $collection = new RouteCollection();
        $collection->addResource(new FileResource($file));

        if (null === $config) {
            return $collection;
        }

        if (!is_array($config)) {
            throw new \InvalidArgumentException(sprintf(
                'The file "%s" must contain a YAML array.', $path
            ));
        }

        foreach ($config as $shortname => $mapping) {
            $parts = explode(':', $shortname);

            if (3 == count($parts)) {
                list($bundle, $class, $action) = $parts;

                $routeName = $this->getRouteName($bundle, $class, $action);
                $route     = $this->getCustomCollectionRoute($bundle, $class, $action);

                $this->overrideRouteParams($shortname, $route, $mapping);
                $collection->add($routeName, $route);

                continue;
            }

            if (1 == count($parts)) {
                $this->parseClassical($collection, $shortname, $mapping, $path, $file);

                continue;
            }

            if (is_array($mapping)) {
                foreach ($mapping as $key => $val) {
                    if (in_array($key, self::$supportedControllerKeys)) {
                        continue;
                    }

                    if ('pattern' === $key) {
                        throw new \InvalidArgumentException(
                            'The `pattern` is only supported for actions, if you want to prefix '.
                            'all the routes of the controller, use `prefix` instead.'
                        );
                    }

                    throw new \InvalidArgumentException(sprintf(
                        '`%s` parameter is not supported by `%s` controller route. Use one of [%s].',
                        $key, $shortname, implode(', ', self::$supportedControllerKeys)
                    ));
                }
            }

            list($bundle, $class) = $parts;

            $prefix                 = $this->getPatternPrefix($class, $mapping);
            $collectionDefaults     = $this->getDefaultsFromMapping($mapping, 'collections');
            $collectionRequirements = $this->getRequirementsFromMapping($mapping, 'collections');
            $collectionOptions      = $this->getOptionsFromMapping($mapping, 'collections');
            $resourceDefaults       = $this->getDefaultsFromMapping($mapping, 'resources');
            $resourceRequirements   = $this->getRequirementsFromMapping($mapping, 'resources');
            $resourceOptions        = $this->getOptionsFromMapping($mapping, 'resources');

            $collectionRoutes = $this->getCollectionRoutesFromMapping($shortname, $mapping, $bundle, $class);
            $resourceRoutes   = $this->getResourceRoutesFromMapping($shortname, $mapping, $bundle, $class);

            $controllerCollection = new RouteCollection();
            foreach ($collectionRoutes as $name => $route) {
                $route->setDefaults(array_merge($collectionDefaults, $route->getDefaults()));
                $route->setRequirements(array_merge(
                    $collectionRequirements, $route->getRequirements()
                ));
                $route->setOptions(array_merge(
                    $collectionOptions, $route->getOptions()
                ));
                $controllerCollection->add($name, $route);
            }
            foreach ($resourceRoutes as $name => $route) {
                $route->setDefaults(array_merge($resourceDefaults, $route->getDefaults()));
                $route->setRequirements(array_merge(
                    $resourceRequirements, $route->getRequirements()
                ));
                $route->setOptions(array_merge(
                    $resourceOptions, $route->getOptions()
                ));
                $controllerCollection->add($name, $route);
            }
            $controllerCollection->addPrefix($prefix);
            $collection->addCollection($controllerCollection);
        }

        return $collection;
    }

    protected function parseClassical(RouteCollection $collection, $shortname,
                                      array $mapping, $path, $file)
    {
        // Symfony 2.2+
        if (method_exists($this, 'validate')) {
            if (isset($mapping['pattern'])) {
                if (isset($mapping['path'])) {
                    throw new \InvalidArgumentException(sprintf(
                        'The file "%s" cannot define both a "path" and a "pattern" attribute. Use only "path".',
                        $path
                    ));
                }

                $mapping['path'] = $mapping['pattern'];
                unset($mapping['pattern']);
            }

            $this->validate($mapping, $shortname, $path);
        // Symfony 2.0, 2.1
        } else {
            foreach ($mapping as $key => $value) {
                if (!in_array($key, $expected = array(
                    'type', 'resource', 'prefix', 'pattern', 'options',
                    'defaults', 'requirements'
                ))) {
                    throw new \InvalidArgumentException(sprintf(
                        'Yaml routing loader does not support given key: "%s". Expected one of the (%s).',
                        $key, implode(', ', $expected)
                    ));
                }
            }
        }

        if (isset($mapping['resource'])) {
            // Symfony 2.2+
            if (method_exists($this, 'parseImport')) {
                $this->parseImport($collection, $mapping, $path, $file);
            // Symfony 2.1
            } else {
                $getOr = function ($key, $def) use ($mapping) {
                    return isset($mapping[$key]) ? $mapping[$key] : $def;
                };

                $type         = $getOr('type', null);
                $prefix       = $getOr('prefix', null);
                $defaults     = $getOr('defaults', array());
                $requirements = $getOr('requirements', array());
                $options      = $getOr('options', array());

                $this->setCurrentDir(dirname($path));
                $resourceCollection = $this->import($mapping['resource'], $type, false, $file);
                $resourceCollection->addPrefix($prefix, $defaults, $requirements);
                $resourceCollection->addOptions($options);
                $collection->addCollection($resourceCollection);
            }
        } else {
            $this->parseRoute($collection, $shortname, $mapping, $path);
        }
    }

    private function getDefaultsFromMapping($mapping, $routeType = 'collections')
    {
        $defaults = array();

        if (!is_array($mapping)) {
            return $defaults;
        }

        if (isset($mapping['defaults'])) {
            $defaults = $mapping['defaults'];
        }

        if (isset($mapping[$routeType]) && is_array($mapping[$routeType])) {
            if (isset($mapping[$routeType]['defaults'])) {
                $defaults = array_merge($defaults, $mapping[$routeType]['defaults']);
            }
        }

        return $defaults;
    }

    private function getRequirementsFromMapping($mapping, $routeType = 'collections')
    {
        $requirements = array();

        if (!is_array($mapping)) {
            return $requirements;
        }

        if (isset($mapping['requirements'])) {
            $requirements = $mapping['requirements'];
        }

        if (isset($mapping[$routeType]) && is_array($mapping[$routeType])) {
            if (isset($mapping[$routeType]['requirements'])) {
                $requirements = array_merge($requirements, $mapping[$routeType]['requirements']);
            }
        }

        return $requirements;
    }

    private function getOptionsFromMapping($mapping, $routeType = 'collections')
    {
        $options = array();

        if (!is_array($mapping)) {
            return $options;
        }

        if (isset($mapping['options'])) {
            $options = $mapping['options'];
        }

        if (isset($mapping[$routeType]) && is_array($mapping[$routeType])) {
            if (isset($mapping[$routeType]['options'])) {
                $options = array_merge($options, $mapping[$routeType]['options']);
            }
        }

        return $options;
    }

    private function getCollectionRoutesFromMapping($shortname, $mapping, $bundle, $class)
    {
        $defaults = $this->getDefaultCollectionRoutes($bundle, $class);
        if (!is_array($mapping) || !isset($mapping['collections'])) {
            return $defaults;
        }

        $collections = $mapping['collections'];
        unset($collections['defaults']);
        unset($collections['requirements']);
        unset($collections['options']);

        if (0 == count($collections)) {
            return $defaults;
        }

        if (false === $collections) {
            return array();
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

            $this->overrideRouteParams($shortname, $route, $params);

            $routes[$routeName] = $route;
        }

        return $routes;
    }

    private function getResourceRoutesFromMapping($shortname, $mapping, $bundle, $class)
    {
        $defaults = $this->getDefaultResourceRoutes($bundle, $class);
        if (!is_array($mapping) || !isset($mapping['resources'])) {
            return $defaults;
        }

        $resources = $mapping['resources'];
        unset($resources['defaults']);
        unset($resources['requirements']);
        unset($resources['options']);

        if (0 == count($resources)) {
            return $defaults;
        }

        if (false === $resources) {
            return array();
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

            $this->overrideRouteParams($shortname, $route, $params);

            $routes[$routeName] = $route;
        }

        return $routes;
    }

    private function overrideRouteParams($shortname, Route $route, $params)
    {
        if (is_array($params)) {
            foreach ($params as $key => $val) {
                if (in_array($key, self::$supportedActionKeys)) {
                    continue;
                }

                throw new \InvalidArgumentException(sprintf(
                    '`%s` parameter is not supported by `%s` action route. Use one of [%s].',
                    $key, $shortname, implode(', ', self::$supportedActionKeys)
                ));
            }
        }

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
            if (isset($params['options'])) {
                $route->setOptions($params['options']);
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
                array('_method' => 'GET')
            ),
            $this->getRouteName($bundle, $class, 'edit') => new Route(
                '/{id}/edit',
                array('_controller' => sprintf('%s:%s:edit', $bundle, $class)),
                array('_method' => 'GET')
            ),
            $this->getRouteName($bundle, $class, 'update') => new Route(
                '/{id}',
                array('_controller' => sprintf('%s:%s:edit', $bundle, $class)),
                array('_method' => 'PUT')
            ),
            $this->getRouteName($bundle, $class, 'delete') => new Route(
                '/{id}',
                array('_controller' => sprintf('%s:%s:delete', $bundle, $class)),
                array('_method' => 'DELETE')
            ),
        );
    }

    private function getCustomResourceRoute($bundle, $class, $action)
    {
        return new Route(
            '/{id}/'.$action,
            array('_controller' => sprintf('%s:%s:%s', $bundle, $class, $action)),
            array('_method' => 'PUT')
        );
    }

    private function getPatternPrefix($class, $mapping)
    {
        if (is_string($mapping)) {
            return $mapping;
        } elseif (is_array($mapping) && isset($mapping['prefix'])) {
            return $mapping['prefix'];
        }

        return '/'.strtolower(str_replace('\\', '/', $class));
    }

    private function getRouteName($bundle, $class, $action)
    {
        $group = implode('_', array_map('lcfirst', explode('\\', $class)));

        return sprintf('%s_%s_%s', lcfirst($bundle), $group, lcfirst($action));
    }
}
