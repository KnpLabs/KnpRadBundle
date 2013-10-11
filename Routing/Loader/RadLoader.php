<?php

namespace Knp\RadBundle\Routing\Loader;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Knp\RadBundle\Routing\Builder\RoutePartBuilderInterface;
use Symfony\Component\Routing\RouteCollection;
use Doctrine\Common\Util\Inflector;
use Symfony\Component\Routing\Route;

class RadLoader implements LoaderInterface
{
    private $builders;
    private $cache;

    static public function getDefaultActions()
    {
        return array(
            'index',
            'new',
            'create',
            'show',
            'edit',
            'update',
            'delete',
        );
    }

    public function __construct()
    {
        $this->builders = array();
        $this->cache    = array();
    }

    public function addBuilder(RoutePartBuilderInterface $builder)
    {
        $this->builders[] = $builder;
    }

    public function load($config, $type = null)
    {
        $baseName = $this->guessBaseName($type);
        $resource = isset($config['resource']) ?
            $config['resource'] :
            $this->guessResourceFromType($type)
        ;
        $actions = array();
        if (!isset($config['actions'])) {
            $actions = $this->definedDefaultActions();
        } else {
            foreach ($config['actions'] as $name => $definition) {
                $actions[$name] = $this->definedDefaultAction($name, $definition);
            }
        }
        $collection = new RouteCollection;

        foreach ($actions as $actionName => $actionDefinition) {
            $route = new Route(null);
            $parents = !isset($config['parent']) ?
                null :
                $this->getParents($config['parent'])
            ;
            foreach ($this->builders as $builder) {
                $builder->build(
                    $route,
                    $baseName,
                    $resource,
                    $actionName,
                    $actionDefinition,
                    $parents
                );
            }
            $routeName = sprintf(
                '%s_%s',
                $baseName,
                strtolower(Inflector::tableize($actionName))
            );

            $collection->add($routeName, $route);

            if (!isset($this->cache[$type])) {
                $this->cache[$type] = array();
            }
            $this->cache[$type][$actionName] = $route;
        }

        return $collection;
    }

    public function supports($config, $type = null)
    {
        return 0 === strpos($type, '@');
    }

    public function getResolver()
    {
    }

    public function setResolver(LoaderResolverInterface $resolver)
    {
    }

    private function guessBaseName($type)
    {
        if (false === strpos($type, ':')) {
            return strtolower(Inflector::tableize(substr($type, 1)));
        } else {
            return implode('_', array_map(
                function ($v) {
                    return strtolower(Inflector::tableize(str_replace(
                        array('/', '\\'),
                        '',
                        $v
                    )));
                },
                explode(':', substr($type, 1))
            ));
        }
    }

    private function guessResourceFromType($type)
    {
        if (false === strpos($type, ':')) {
            throw new \InvalidArgumentException(sprintf(
                'Unable to defined a resource with the type %s. Please, precised'.
                ' a valid resource (BundleName:ResourceName)',
                $type
            ));
        }

        return substr($type, 1);
    }

    private function definedDefaultActions()
    {
        $actions = array();

        foreach (self::getDefaultActions() as $name) {
            $actions[$name] = $this->definedDefaultAction($name);
        }

        return $actions;
    }

    private function definedDefaultAction($name, $definition = null)
    {
        if (null === $definition and !in_array($name, self::getDefaultActions())) {
            throw new \InvalidArgumentException(sprintf(
                'No default actions has been found for %s. Please precised your
                action definition.',
                $name
            ));
        }

        if (null !== $definition) {
            return $definition;
        }

        return array();
    }

    private function getParents($name)
    {
        if (!isset($this->cache[$name])) {
            return null;
        }

        return $this->cache[$name];
    }
}
