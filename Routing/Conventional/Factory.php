<?php

namespace Knp\RadBundle\Routing\Conventional;

use Knp\RadBundle\Routing\Conventional\Generator;
use Symfony\Component\Routing\Route;

class Factory
{
    private $patterns;
    private $controllerNames;
    private $routes;
    private $viewNames;

    public function __construct(Generator\Pattern $patterns = null, Generator\ControllerName $controllerNames = null, Generator\RouteName $routes = null, Generator\ViewName $viewNames = null)
    {
        $this->patterns        = $patterns ?: new Generator\Pattern;
        $this->controllerNames = $controllerNames ?: new Generator\ControllerName;
        $this->routes          = $routes ?: new Generator\RouteName;
        $this->viewNames       = $viewNames ?: new Generator\ViewName;
    }

    public function create(Config $config)
    {
        $name = $this->routes->generate($config);

        return array($name, new Route(
            $this->patterns->generate($config),
            array_merge(array(
                '_controller' => $this->controllerNames->generate($config),
                '_view'       => $this->viewNames->generate($config),
            ), $config->getDefaults()),
            $config->getRequirements(),
            array(), // options
            null, // host
            array(), // schemes
            $config->getMethods()
        ));
    }
}
