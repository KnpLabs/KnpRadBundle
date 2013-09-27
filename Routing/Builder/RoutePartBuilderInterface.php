<?php

namespace Knp\RadBundle\Routing\Builder;

use Symfony\Component\Routing\Route;

interface RoutePartBuilderInterface
{
    public function build(
        Route $route,
        $baseName,
        $resource,
        $actionName,
        array $actionDefinition = null,
        array $parent = null
    );
}
