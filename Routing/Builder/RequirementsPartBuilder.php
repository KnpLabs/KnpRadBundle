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
        array $parents = null
    ) {
        if (null !== $actionDefinition and isset($actionDefinition['requirements'])) {
            if (null !== $parents and isset($parents[$actionName])) {
                return $route->addRequirements(array_merge(
                    $parents[$actionName]->getRequirements(),
                    $actionDefinition['requirements']
                ));
            }

            return $route->addRequirements($actionDefinition['requirements']);
        }

        return $route;
    }
}
