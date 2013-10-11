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
        array $parent = null
    ) {
        if (null !== $actionDefinition and isset($actionDefinition['requirements'])) {
            if (null !== $parent and
                isset($parent['requirements']) and
                is_array($parent['requirements'])
            ) {
                return $route->addRequirements(array_merge(
                    $parent['requirements'],
                    $actionDefinition['requirements']
                ));
            }

            return $route->addRequirements($actionDefinition['requirements']);
        }

        return $route;
    }
}
