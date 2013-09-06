<?php

namespace Knp\RadBundle\Resource\Resolver;

use Symfony\Component\HttpFoundation\Request;
use Knp\RadBundle\HttpFoundation\RequestManipulator;

class RequestResolver
{
    public function __construct(ResourceResolver $resourceResolver, RequestManipulator $requestManipulator = null)
    {
        $this->resourceResolver = $resourceResolver;
        $this->requestManipulator = $requestManipulator ?: new RequestManipulator();
    }

    public function resolveResource(Request $request, $name)
    {
        $options = $this->getResourceOptions($request, $name);
        $resource = $this->resourceResolver->resolveResource($request, $options);
        $this->setResource($request, $name, $resource);

        return $resource;
    }

    public function hasResourceOptions(Request $request, $name)
    {
        if (false === $this->requestManipulator->hasAttribute($request, '_resources')) {
            return false;
        }

        $resources = $this->requestManipulator->getAttribute($request, '_resources');

        return array_key_exists($name, $resources);
    }

    public function getResourceOptions(Request $request, $name)
    {
        $resources = $this->requestManipulator->getAttribute($request, '_resources');

        return $resources[$name];
    }

    public function setResource(Request $request, $name, $resource)
    {
        $this->requestManipulator->setAttribute($request, $name, $resource);
    }
}
