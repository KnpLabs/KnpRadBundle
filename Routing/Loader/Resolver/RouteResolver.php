<?php

namespace Knp\RadBundle\Routing\Loader\Resolver;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * Contains many route loaders and match a loader for a given
 * route key
 */
class RouteResolver implements LoaderResolverInterface
{
    private $loaders;

    public function __construct()
    {
        $this->loaders = [];
    }

    /**
     * Add a resolver to the collection
     *
     * @param LoaderInterface $loader
     */
    public function add(LoaderInterface $loader)
    {
        $this->loaders[] = $loader;
    }

    /**
     * Resolve a given route
     *
     * @param mixed $resource, The route content
     * @param string $type = null, The route name
     *
     * @return RouteCollection
     */
    public function resolve($resource, $type = null)
    {
        foreach ($this->loaders as $loader) {
            if ($loader->supports($type)) {

                return $loader->load(array($type => $resource));
            }
        }

        throw new RouteNotFoundException(sprintf(
            'The route with type %s does not have a matching loader :(',
            $type
        ));
    }
}
