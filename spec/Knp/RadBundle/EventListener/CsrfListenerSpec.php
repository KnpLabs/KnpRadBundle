<?php

namespace spec\Knp\RadBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument as Arg;

class CsrfListenerSpec extends ObjectBehavior
{
    /**
     * @param Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface $csrfProvider
     * @param Symfony\Component\HttpKernel\Event\GetResponseEvent                      $event
     * @param Symfony\Component\HttpFoundation\Request                                 $request
     * @param Symfony\Component\HttpFoundation\ParameterBag                            $requestBag
     * @param Symfony\Component\HttpFoundation\ParameterBag                            $attributeBag
     */
    function let($csrfProvider, $event, $request, $requestBag, $attributeBag)
    {
        $event->getRequest()->willReturn($request);
        $request->request = $requestBag;
        $request->attributes = $attributeBag;

        $this->beConstructedWith($csrfProvider);
    }

    function its_onKernelRequest_should_continue_if_csrf_valid($event, $request, $requestBag, $attributeBag, $csrfProvider)
    {
        $attributeBag->get('_check_csrf', false)->shouldBeCalled()->willReturn(true);
        $requestBag->has('_link_token')->shouldBeCalled()->willReturn(true);
        $requestBag->get('_link_token')->shouldBeCalled()->willReturn('some token');
        $csrfProvider->isCsrfTokenValid('link', 'some token')->shouldBeCalled()->willReturn(true);

        $this->onKernelRequest($event);
    }

    function its_onKernelRequest_should_continue_if_no_csrf_provided_and_check_csrf_disabled($event, $request, $requestBag, $attributeBag, $csrfProvider)
    {
        $attributeBag->get('_check_csrf', false)->shouldBeCalled()->willReturn(false);
        $requestBag->get('_link_token')->shouldNotBeCalled();
        $csrfProvider->isCsrfTokenValid('link', Arg::type('string'))->shouldNotBeCalled();

        $this->onKernelRequest($event);
    }

    function its_onKernelRequest_should_throw_exception_if_no_csrf_provided_and_check_csrf_enabled($event, $request, $requestBag, $attributeBag, $csrfProvider)
    {
        $attributeBag->get('_check_csrf', false)->shouldBeCalled()->willReturn(true);
        $requestBag->has('_link_token')->shouldBeCalled()->willReturn(false);
        $requestBag->get('_link_token')->shouldNotBeCalled();
        $csrfProvider->isCsrfTokenValid('link', Arg::type('string'))->shouldNotBeCalled();

        $this->shouldThrow(new \InvalidArgumentException(
            'The CSRF token verification is activated but you did not send a token. Please submit a request with a valid csrf token.'
        ))->duringOnKernelRequest($event);
    }

    function its_onKernelRequest_should_throw_exception_if_csrf_invalid($event, $request, $requestBag, $attributeBag, $csrfProvider)
    {
        $attributeBag->get('_check_csrf', false)->shouldBeCalled()->willReturn(true);
        $requestBag->has('_link_token')->shouldBeCalled()->willReturn(true);
        $requestBag->get('_link_token')->shouldBeCalled()->willReturn('some token');
        $csrfProvider->isCsrfTokenValid('link', 'some token')->shouldBeCalled()->willReturn(false);

        $this->shouldThrow(new \InvalidArgumentException(
            'The CSRF token is invalid. Please submit a request with a valid csrf token.'
        ))->duringOnKernelRequest($event);
    }

    function its_onKernelRequest_should_use_the_link_string_as_csrf_intention($event, $request, $requestBag, $attributeBag, $csrfProvider)
    {
        $attributeBag->get('_check_csrf', false)->shouldBeCalled()->willReturn(true);
        $requestBag->has('_link_token')->shouldBeCalled()->willReturn(true);
        $requestBag->get('_link_token')->shouldBeCalled()->willReturn('some token');
        $csrfProvider->isCsrfTokenValid('link', 'some token')->shouldBeCalled()->willReturn(true);

        $this->onKernelRequest($event);
    }
}
