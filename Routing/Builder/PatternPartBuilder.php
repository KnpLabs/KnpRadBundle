<?php

namespace Knp\RadBundle\Routing\Builder;

use Symfony\Component\Routing\Route;
use Doctrine\Common\Util\Inflector;
use Knp\RadBundle\Routing\Loader\RadLoader;

class PatternPartBuilder implements RoutePartBuilderInterface
{
    public function build(
        Route $route,
        $baseName,
        $resource,
        $actionName,
        array $actionDefinition = null,
        array $parents = null
    ) {
        $parentPattern = null === $parents ? '' : $this->getParentPattern($parents, $actionName);
        if (null !== $actionDefinition and isset($actionDefinition['pattern'])) {
            return $this->setPattern($route, $parentPattern.$actionDefinition['pattern']);
        }

        $resources = explode(':', $resource);
        if (2 !== count($resources)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid resource %s. A resource must be defined with '.
                'this structure : BundleName:Resource',
                $resource
            ));
        }

        if (!in_array($actionName, RadLoader::getDefaultActions())) {
            $pattern = sprintf(
                '/%s/%s',
                implode('/', array_map(
                    function ($v) {
                        return strtolower(Inflector::pluralize(Inflector::tableize($v)));
                    },
                    explode('/', str_replace('\\', '/', $resources[1]))
                )),
                Inflector::tableize($actionName)
            );
        } else {
            $pattern = $this->createDefaultPattern($actionName, $resources[1]);
        }

        return $this->setPattern($route, $parentPattern.$pattern);
    }

    private function setPattern(Route $route, $pattern)
    {
        if (method_exists($route, 'setPath')) {
            $route->setPath($pattern);
        } else {
            $route->setPattern($pattern);
        }

        return $route;
    }

    private function getParentPattern(array $parents, $actionName)
    {
        if (isset($parents['show'])) {
            return $this->getPattern($parents['show']);
        }
        if (isset($parents['update'])) {
            return $this->getPattern($parents['update']);
        }
        if (isset($parents['delete'])) {
            return $this->getPattern($parents['delete']);
        }

        return '';
    }

    private function getPattern(Route $route)
    {
        if (method_exists($route, 'getPath')) {
            return $route->getPath();
        } else {
            return $route->getPattern();
        }
    }

    private function createDefaultPattern($actionName, $resource)
    {
        $basePattern = sprintf(
            '/%s',
            implode('/', array_map(
                function ($v) {
                    return strtolower(Inflector::pluralize(Inflector::tableize($v)));
                },
                explode('/', str_replace('\\', '/', $resource))
            ))
        );

        $entity = str_replace('\\', '/', $resource);

        switch ($actionName) {
            case 'index':
                return $basePattern;
            case 'new':
                return sprintf('%s/new', $basePattern);
            case 'create':
                return $basePattern;
            case 'show':
                return sprintf('%s/{%s_id}', $basePattern, $entity);
            case 'edit':
                return sprintf('%s/{%s_id}/edit', $basePattern, $entity);
            case 'update':
                return sprintf('%s/{%s_id}', $basePattern, $entity);
            case 'delete':
                return sprintf('%s/{%s_id}', $basePattern, $entity);
        }
    }
}
