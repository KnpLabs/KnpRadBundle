<?php

namespace spec\Knp\RadBundle\EventListener;

use PhpSpec\ObjectBehavior;

class CsrfListenerSpec extends ObjectBehavior
{
    /**
     * @param Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface $csrfProvider
     * @param Symfony\Component\HttpKernel\Event\GetResponseEvent                      $event
     * @param Symfony\Component\HttpFoundation\Request                                 $request
     * @param Symfony\Component\HttpFoundation\ParameterBag                            $requestBag
     */
    function let($csrfProvider, $event, $request, $requestBag)
    {
        $event->getRequest()->willReturn($request);
        $request->request = $requestBag;

        $this->beConstructedWith($csrfProvider);
    }

    function its_onKernelRequest_should_continue_if_csrf_valid($event, $request, $requestBag, $csrfProvider)
    {
        $requestBag->has('_link_token')->shouldBeCalled()->willReturn(true);
        $requestBag->get('_link_token')->shouldBeCalled()->willReturn('some token');
        $csrfProvider->isCsrfTokenValid('link', 'some token')->shouldBeCalled()->willReturn(true);

        $this->onKernelRequest($event);
    }

    function its_onKernelRequest_should_continue_if_no_csrf_provided($event, $request, $requestBag, $csrfProvider)
    {
        $requestBag->has('_link_token')->shouldBeCalled()->willReturn(false);
        $requestBag->get('_link_token')->shouldNotBeCalled();
        $csrfProvider->isCsrfTokenValid('link', 'some token')->shouldNotBeCalled();

        $this->onKernelRequest($event);
    }

    function its_onKernelRequest_should_throw_exception_if_csrf_invalid($event, $request, $requestBag, $csrfProvider)
    {
        $requestBag->has('_link_token')->shouldBeCalled()->willReturn(true);
        $requestBag->get('_link_token')->shouldBeCalled()->willReturn('some token');
        $csrfProvider->isCsrfTokenValid('link', 'some token')->shouldBeCalled()->willReturn(false);

        $this->shouldThrow(new \InvalidArgumentException(
            'The CSRF token is invalid. Please try to resubmit the form.'
        ))->duringOnKernelRequest($event);
    }

    function its_onKernelRequest_should_use_request_method_as_csrf_intention($event, $request, $requestBag, $csrfProvider)
    {
        $requestBag->has('_link_token')->shouldBeCalled()->willReturn(true);
        $requestBag->get('_link_token')->shouldBeCalled()->willReturn('some token');
        $csrfProvider->isCsrfTokenValid('link', 'some token')->shouldBeCalled()->willReturn(true);

        $this->onKernelRequest($event);
    }
}
