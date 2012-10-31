<?php

namespace Knp\RadBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Knp\RadBundle\Resource\Resolver\RequestResolver;

class ResourceResolverListener
{
    private $requestResolver;

    public function __construct(RequestResolver $requestResolver)
    {
        $this->requestResolver = $requestResolver;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $this->requestResolver->resolveRequest($request);
    }
}
