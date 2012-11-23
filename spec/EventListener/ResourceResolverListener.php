<?php

namespace spec\Knp\RadBundle\EventListener;

use PHPSpec2\ObjectBehavior;

class ResourceResolverListener extends ObjectBehavior
{
    /**
     * @param Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     * @param Symfony\Component\HttpFoundation\Request $request
     * @param Knp\RadBundle\Resource\Resolver\RequestResolver $requestResolver
     */
    function let($event, $request, $requestResolver)
    {
        $this->beConstructedWith($requestResolver);
    }


    function it_should_resolve_resources_on_kernel_request($event, $request, $requestResolver)
    {
        $event->getRequest()->shouldBeCalled()->willReturn($request);

        $requestResolver->resolveRequest($request)->shouldBeCalled();

        $this->onKernelRequest($event);
    }

    function it_should_transform_resource_resolution_failures_into_404_errors($event, $request, $requestResolver)
    {
        $event->getRequest()->shouldBeCalled()->willReturn($request);

        $requestResolver->resolveRequest($request)->willThrow('Knp\RadBundle\Resource\Resolver\ResolutionFailureException');

        $this->shouldThrow('Symfony\Component\HttpKernel\Exception\NotFoundHttpException')->duringOnKernelRequest($event);
    }
}
