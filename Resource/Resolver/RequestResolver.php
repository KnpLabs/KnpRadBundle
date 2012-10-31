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

    public function resolveRequest(Request $request)
    {
        if (false === $this->requestManipulator->hasAttribute($request, '_resources')) {
            return;
        }

        $resources = $this->requestManipulator->getAttribute($request, '_resources');

        foreach ($resources as $name => $options) {
            $resource = $this->resourceResolver->resolveResource($request, $options);
            $this->requestManipulator->setAttribute($request, $name, $resource);
        }
    }
}
