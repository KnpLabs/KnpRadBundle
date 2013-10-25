<?php

namespace Knp\RadBundle\Routing\Builder;

use Knp\RadBundle\Routing\Builder\RoutePartBuilderInterface;
use Symfony\Component\Routing\Route;

class ControllerPartBuilder implements RoutePartBuilderInterface
{
    public function build(
        Route $route,
        $baseName,
        $resource,
        $actionName,
        array $actionDefinition = null,
        array $parents = null
    ) {
        if (null !== $actionDefinition and
            isset($actionDefinition['defaults']) and
            is_array($actionDefinition['defaults'])
        ) {
            if (isset($actionDefinition['defaults']['_controller'])) {
                return $route->addDefaults(array(
                    '_controller' => $actionDefinition['defaults']['_controller']
                ));
            }
        }
        $controller = sprintf('%s:%s', $resource, $actionName);

        return $route->addDefaults(array(
            '_controller' => $controller
        ));
    }
}
