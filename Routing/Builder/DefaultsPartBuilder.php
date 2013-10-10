<?php

namespace Knp\RadBundle\Routing\Builder;

use Knp\RadBundle\Routing\Builder\RoutePartBuilderInterface;
use Symfony\Component\Routing\Route;

class DefaultsPartBuilder implements RoutePartBuilderInterface
{
    public function build(
        Route $route,
        $baseName,
        $resource,
        $actionName,
        array $actionDefinition = null,
        array $parent = null
    ) {
        if (null !== $actionDefinition['defaults'] and is_array($actionDefinition['defaults'])) {
            if (null !== $parent and isset($parent['defaults'])) {
                return $route->addDefaults(array_merge(
                    $parent['defaults'],
                    $actionDefinition['defaults']
                ));
            } else {
                return $route->addDefaults($actionDefinition['defaults']);
            }
        }

        return $route;
    }
}
