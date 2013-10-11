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
        Route $parent = null
    ) {
        if (null !== $actionDefinition and
            isset($actionDefinition['defaults']) and
            is_array($actionDefinition['defaults'])
        ) {
            if (null !== $parent) {
                return $route->addDefaults(array_merge(
                    $parent->getDefaults(),
                    $actionDefinition['defaults']
                ));
            } else {
                return $route->addDefaults($actionDefinition['defaults']);
            }
        }

        return $route;
    }
}
