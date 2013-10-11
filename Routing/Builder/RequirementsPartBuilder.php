<?php

namespace Knp\RadBundle\Routing\Builder;

use Symfony\Component\Routing\Route;

class RequirementsPartBuilder implements RoutePartBuilderInterface
{
    public function build(
        Route $route,
        $baseName,
        $resource,
        $actionName,
        array $actionDefinition = null,
        Route $parent = null
    ) {
        if (null !== $actionDefinition and isset($actionDefinition['requirements'])) {
            if (null !== $parent) {
                return $route->addRequirements(array_merge(
                    $parent->getRequirements(),
                    $actionDefinition['requirements']
                ));
            }

            return $route->addRequirements($actionDefinition['requirements']);
        }

        return $route;
    }
}
