<?php

namespace Knp\RadBundle\Routing\Conventional\Generator;

use Knp\RadBundle\Routing\Conventional\Config;
use Knp\RadBundle\Routing\Conventional\Generator;

class RouteName implements Generator
{
    /**
     * recursively build a route name
     *
     * @return string
     **/
    public function generate(Config $config)
    {
        $parts = array();
        if ($config->parent) {
            $parent = $config->parent->isRepresentant() ? $config->parent->parent : $config->parent;
            $parts[] = $this->generate($parent);
        }

        $parts[] = $this->getPrefix($config);

        return implode('_', array_filter($parts));
    }

    /**
     * normalize name, removing root prefix (i.e: App) for nested routes
     *
     * @return string
     **/
    private function getPrefix(Config $config)
    {
        $parts = explode(':', $config->name);
        if ($config->parent && 2 === count($parts)) {
            // nested route that contains 'App:*
            array_shift($parts);
        }

        return strtolower(str_replace(array('/', '\\', ':'), '_', implode('_', $parts)));
    }
}
