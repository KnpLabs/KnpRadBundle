<?php

namespace Knp\RadBundle\Routing\Loader\Resolver;

/**
 * This interface defined a common way to resolve a route from a standard
 * rad_convention yaml file
 */
interface RouteResolverInterface
{
    /**
     * Test if the route key is supported
     *
     * @param string $key
     *
     * @return boolean
     */
    public function supports($key);

    /**
     * Load the route and must return an instance of RouteCollection
     *
     * @param string $key
     * @param array  $config
     *
     * @return RouteCollection
     */
    public function load($key, array $config);
}
