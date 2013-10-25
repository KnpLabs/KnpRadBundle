<?php

namespace Knp\RadBundle\Routing\Builder;

use Knp\RadBundle\Routing\Loader\RadLoader;
use Symfony\Component\Routing\Route;

class MethodPartBuilder implements RoutePartBuilderInterface
{
    public function build(
        Route $route,
        $baseName,
        $resource,
        $actionName,
        array $actionDefinition = null,
        array $parents = null
    ) {
        if (null !== $actionDefinition and isset($actionDefinition['methods'])) {
            $route->setMethods($actionDefinition['methods']);
            return $route;
        }

        if (null !== $parents and isset($parents[$actionName])) {
            $route->setMethods($parents[$actionName]->getMethods());
            return $route;
        }

        if (in_array($actionName, RadLoader::getDefaultActions())) {
            $route->setMethods($this->getDefaultMethod($actionName));
        } else {
            $route->setMethods('GET');
        }

        return $route;
    }

    private function getDefaultMethod($actionName)
    {
        if (in_array($actionName, array('index', 'new', 'show', 'edit'))) {
            return 'GET';
        }

        if ($actionName == 'create') {
            return 'POST';
        }

        if ($actionName == 'update') {
            return array('PUT', 'PATCH');
        }

        return 'DELETE';
    }
}
