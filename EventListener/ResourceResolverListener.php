<?php

namespace Knp\RadBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Knp\RadBundle\Resource\Resolver\RequestResolver;
use Knp\RadBundle\Resource\Resolver\ResolutionFailureException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

        try {
            $this->requestResolver->resolveRequest($request);
        } catch (ResolutionFailureException $e) {
            throw new NotFoundHttpException('Unable to resolve resources.', $e);
        }
    }
}
