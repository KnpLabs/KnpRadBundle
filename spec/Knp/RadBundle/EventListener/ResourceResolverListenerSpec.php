<?php

namespace spec\Knp\RadBundle\EventListener;

use PhpSpec\ObjectBehavior;

class ResourceResolverListenerSpec extends ObjectBehavior
{
    /**
     * @param Symfony\Component\HttpKernel\Event\FilterControllerEvent $event
     * @param Symfony\Component\HttpFoundation\Request $request
     * @param Knp\RadBundle\Resource\Resolver\RequestResolver $requestResolver
     * @param Knp\RadBundle\Reflection\ReflectionFactory $reflectionFactory
     */
    function let($requestResolver, $reflectionFactory)
    {
        $this->beConstructedWith($requestResolver, $reflectionFactory);
    }

    /**
     * @param \ReflectionParameter $param1
     * @param \ReflectionParameter $param2
     **/
    function it_should_resolve_resources_on_kernel_controller($event, $request, $requestResolver, $reflectionFactory, $param1, $param2)
    {
        $param1->getName()->willReturn('session');
        $param2->getName()->willReturn('blogPost');
        $reflectionFactory->getParameters(array('Test', 'test'))->shouldBeCalled()->willReturn(array(
            $param1, $param2
        ));
        $event->getRequest()->shouldBeCalled()->willReturn($request);
        $event->getController()->shouldBeCalled()->willReturn(array('Test', 'test'));
        $requestResolver->hasResourceOptions($request, 'session')->shouldBeCalled()->willReturn(true);
        $requestResolver->hasResourceOptions($request, 'blogPost')->shouldBeCalled()->willReturn(true);
        $requestResolver->resolveResource($request, 'session')->shouldBeCalled();
        $requestResolver->resolveResource($request, 'blogPost')->shouldBeCalled();

        $this->onKernelController($event);
    }

    /**
     * @param \ReflectionParameter $param1
     * @param \ReflectionParameter $param2
     **/
    function it_should_transform_resource_resolution_failures_into_404_errors($event, $request, $requestResolver, $reflectionFactory, $param1, $param2)
    {
        $reflectionFactory->getParameters(array('Test', 'test'))->shouldBeCalled()->willReturn(array(
            $param1, $param2
        ));
        $param1->getName()->willReturn('session');
        $param1->isOptional()->willReturn(false);
        $param2->getName()->willReturn('blogPost');
        $event->getRequest()->shouldBeCalled()->willReturn($request);
        $event->getController()->shouldBeCalled()->willReturn(array('Test', 'test'));

        $requestResolver->hasResourceOptions($request, 'session')->shouldBeCalled()->willReturn(true);

        $requestResolver->resolveResource($request, 'session')->willThrow('Knp\RadBundle\Resource\Resolver\ResolutionFailureException');

        $this->shouldThrow('Symfony\Component\HttpKernel\Exception\NotFoundHttpException')->duringOnKernelController($event);
    }

    /**
     * @param \ReflectionParameter $param1
     * @param \ReflectionParameter $param2
     **/
    function it_should_continue_resolution_if_param_optional($event, $request, $requestResolver, $reflectionFactory, $param1, $param2)
    {
        $reflectionFactory->getParameters(array('Test', 'test'))->shouldBeCalled()->willReturn(array(
            $param1, $param2
        ));
        $param1->getName()->willReturn('session');
        $param1->isOptional()->willReturn(true);
        $param2->getName()->willReturn('blogPost');
        $param2->isOptional()->willReturn(false);
        $event->getRequest()->shouldBeCalled()->willReturn($request);
        $event->getController()->shouldBeCalled()->willReturn(array('Test', 'test'));

        $requestResolver->hasResourceOptions($request, 'session')->shouldBeCalled()->willReturn(true);
        $requestResolver->hasResourceOptions($request, 'blogPost')->shouldBeCalled()->willReturn(true);

        $requestResolver->resolveResource($request, 'session')->shouldBeCalled();
        $requestResolver->resolveResource($request, 'blogPost')->willThrow('Knp\RadBundle\Resource\Resolver\ResolutionFailureException');

        $this->shouldThrow('Symfony\Component\HttpKernel\Exception\NotFoundHttpException')->duringOnKernelController($event);
    }
}
