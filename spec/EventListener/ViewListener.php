<?php

namespace spec\Knp\RadBundle\EventListener;

use PHPSpec2\ObjectBehavior;

class ViewListener extends ObjectBehavior
{
    /**
     * @param Symfony\Component\HttpFoundation\Request $request
     * @param Symfony\Component\HttpFoundation\Response $response
     * @param Symfony\Bundle\FrameworkBundle\Templating\EngineInterface $engine
     * @param Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser $cnp
     * @param Knp\RadBundle\HttpFoundation\RequestManipulator $reqManip
     * @param Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent $event
     * @param Knp\RadBundle\EventListener\MissingViewHandler $mvh
     */
    function let($request, $response, $engine, $cnp, $reqManip, $event, $mvh)
    {
        $this->beConstructedWith($engine, $cnp, 'twig', 'App', $mvh, $reqManip);

        $event->getRequest()->willReturn($request);
    }

    function it_should_create_a_view_response_when_controller_did_not_return_any($request, $response, $reqManip, $engine, $event, $mvh)
    {
        $reqManip->hasAttribute($request, '_controller')->willReturn(true);
        $reqManip->getAttribute($request, '_controller')->willReturn('App\Controller\CheeseController::eatAction');

        $request->getRequestFormat()->willReturn('html');

        $engine->exists('App:Cheese:eat.html.twig')->willReturn(true);
        $engine->renderResponse('App:Cheese:eat.html.twig', array('foo' => 'bar'))->willReturn($response);

        $event->setResponse($response)->shouldBeCalled();
        $event->getRequest()->willReturn($request);
        $event->getControllerResult()->willReturn(array('foo' => 'bar'));

        $mvh->handleMissingView(ANY_ARGUMENTS)->shouldNotBeCalled();

        $this->onKernelView($event);
    }

    function it_should_create_a_view_response_when_controller_return_null($request, $response, $reqManip, $engine, $event, $mvh)
    {
        $reqManip->hasAttribute($request, '_controller')->willReturn(true);
        $reqManip->getAttribute($request, '_controller')->willReturn('App\Controller\CheeseController::eatAction');

        $request->getRequestFormat()->willReturn('html');

        $engine->exists('App:Cheese:eat.html.twig')->willReturn(true);
        $engine->renderResponse('App:Cheese:eat.html.twig', array())->willReturn($response);

        $event->setResponse($response)->shouldBeCalled();
        $event->getRequest()->willReturn($request);
        $event->getControllerResult()->willReturn(null);

        $mvh->handleMissingView(ANY_ARGUMENTS)->shouldNotBeCalled();

        $this->onKernelView($event);
    }

    function it_should_resolve_controller_when_not_yet_resolved($request, $response, $reqManip, $engine, $event, $cnp, $mvh)
    {
        $reqManip->hasAttribute($request, '_controller')->willReturn(true);
        $reqManip->getAttribute($request, '_controller')->willReturn('App\Controller\CheeseController::eatAction');

        $request->getRequestFormat()->willReturn('html');

        $cnp->parse('App:Cheese:eat')->willReturn('App\Controller\CheeseController::eatAction');

        $engine->exists('App:Cheese:eat.html.twig')->willReturn(true);
        $engine->renderResponse('App:Cheese:eat.html.twig', array('foo' => 'bar'))->willReturn($response);

        $event->setResponse($response)->shouldBeCalled();
        $event->getRequest()->willReturn($request);
        $event->getControllerResult()->willReturn(array('foo' => 'bar'));

        $mvh->handleMissingView(ANY_ARGUMENTS)->shouldNotBeCalled();

        $this->onKernelView($event);
    }

    function it_should_use_the_right_template_depending_on_request_format($request, $response, $reqManip, $engine, $event, $cnp, $mvh)
    {
        $reqManip->hasAttribute($request, '_controller')->willReturn(true);
        $reqManip->getAttribute($request, '_controller')->willReturn('App\Controller\CheeseController::eatAction');

        $request->getRequestFormat()->willReturn('xml');

        $engine->exists('App:Cheese:eat.xml.twig')->willReturn(true);
        $engine->renderResponse('App:Cheese:eat.xml.twig', array('foo' => 'bar'))->willReturn($response);

        $event->setResponse($response)->shouldBeCalled();
        $event->getRequest()->willReturn($request);
        $event->getControllerResult()->willReturn(array('foo' => 'bar'));

        $mvh->handleMissingView(ANY_ARGUMENTS)->shouldNotBeCalled();

        $this->onKernelView($event);
    }

    function it_should_abort_when_controller_is_not_in_request_attributes($reqManip, $request, $event)
    {
        $reqManip->hasAttribute($request, '_controller')->willReturn(false);

        $event->getControllerResult()->willReturn(array('foo' => 'bar'));
        $event->setResponse(ANY_ARGUMENTS)->shouldNotBeCalled();

        $this->onKernelView($event);
    }

    function it_should_forward_event_to_missing_view_handler_when_view_does_not_exist($request, $response, $reqManip, $engine, $event, $cnp, $mvh)
    {
        $reqManip->hasAttribute($request, '_controller')->willReturn(true);
        $reqManip->getAttribute($request, '_controller')->willReturn('App\Controller\CheeseController::eatAction');

        $request->getRequestFormat()->willReturn('html');

        $event->getControllerResult()->willReturn(array('foo' => 'bar'));

        $engine->exists('App:Cheese:eat.html.twig')->willReturn(false);

        $mvh->handleMissingView($event, 'App:Cheese:eat.html.twig', array('foo' => 'bar'))->shouldBeCalled();

        $this->onKernelView($event);
    }

    function it_should_deduce_view_with_correct_bundle_name($request, $response, $reqManip, $engine, $event, $cnp, $mvh)
    {
        $this->beConstructedWith($engine, $cnp, 'twig', 'TestBundle', $mvh, $reqManip);

        $reqManip->hasAttribute($request, '_controller')->willReturn(true);
        $reqManip->getAttribute($request, '_controller')->willReturn('App\Controller\CheeseController::eatAction');

        $request->getRequestFormat()->willReturn('html');

        $event->getControllerResult()->willReturn(array('foo' => 'bar'));

        $engine->exists('TestBundle:Cheese:eat.html.twig')->willReturn(false);

        $mvh->handleMissingView($event, 'TestBundle:Cheese:eat.html.twig', array('foo' => 'bar'))->shouldBeCalled();

        $this->onKernelView($event);
    }
}
