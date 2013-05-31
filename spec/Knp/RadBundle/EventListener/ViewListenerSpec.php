<?php

namespace spec\Knp\RadBundle\EventListener;

use PhpSpec\ObjectBehavior;

class ViewListenerSpec extends ObjectBehavior
{
    /**
     * @param Symfony\Component\HttpFoundation\Request $request
     * @param Symfony\Component\HttpFoundation\Response $response
     * @param Symfony\Bundle\FrameworkBundle\Templating\EngineInterface $engine
     * @param Twig_Environment $twig
     * @param Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser $cnp
     * @param Knp\RadBundle\HttpFoundation\RequestManipulator $reqManip
     * @param Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent $event
     * @param Knp\RadBundle\EventListener\MissingViewHandler $mvh
     */
    function let($request, $twig, $response, $engine, $cnp, $reqManip, $event, $mvh)
    {
        $this->beConstructedWith($engine, $twig, $cnp, 'twig', 'App', $mvh, $reqManip);

        $event->getRequest()->willReturn($request);
    }

    function it_should_create_a_view_response_when_controller_did_not_return_any($request, $response, $reqManip, $engine, $twig, $event, $mvh)
    {
        $reqManip->hasAttribute($request, '_controller')->willReturn(true);
        $reqManip->getAttribute($request, '_controller')->willReturn('App\Controller\CheeseController::eatAction');

        $request->getRequestFormat()->willReturn('html');

        $twig->loadTemplate('App:Cheese:eat.html.twig')->willReturn();
        $engine->renderResponse('App:Cheese:eat.html.twig', array('foo' => 'bar'))->willReturn($response);

        $event->setResponse($response)->shouldBeCalled();
        $event->getRequest()->willReturn($request);
        $event->getControllerResult()->willReturn(array('foo' => 'bar'));

        $mvh->handleMissingView(\Prophecy\Argument::cetera())->shouldNotBeCalled();

        $this->onKernelView($event);
    }

    function it_should_create_a_view_response_when_controller_return_null($request, $response, $reqManip, $engine, $twig, $event, $mvh)
    {
        $reqManip->hasAttribute($request, '_controller')->willReturn(true);
        $reqManip->getAttribute($request, '_controller')->willReturn('App\Controller\CheeseController::eatAction');

        $request->getRequestFormat()->willReturn('html');

        $twig->loadTemplate('App:Cheese:eat.html.twig')->willReturn();
        $engine->renderResponse('App:Cheese:eat.html.twig', array())->willReturn($response);

        $event->setResponse($response)->shouldBeCalled();
        $event->getRequest()->willReturn($request);
        $event->getControllerResult()->willReturn(null);

        $mvh->handleMissingView(\Prophecy\Argument::cetera())->shouldNotBeCalled();

        $this->onKernelView($event);
    }

    function it_should_resolve_controller_when_not_yet_resolved($request, $response, $reqManip, $engine, $twig, $event, $cnp, $mvh)
    {
        $reqManip->hasAttribute($request, '_controller')->willReturn(true);
        $reqManip->getAttribute($request, '_controller')->willReturn('App\Controller\CheeseController::eatAction');

        $request->getRequestFormat()->willReturn('html');

        $cnp->parse('App:Cheese:eat')->willReturn('App\Controller\CheeseController::eatAction');

        $twig->loadTemplate('App:Cheese:eat.html.twig')->willReturn();
        $engine->renderResponse('App:Cheese:eat.html.twig', array('foo' => 'bar'))->willReturn($response);

        $event->setResponse($response)->shouldBeCalled();
        $event->getRequest()->willReturn($request);
        $event->getControllerResult()->willReturn(array('foo' => 'bar'));

        $mvh->handleMissingView(\Prophecy\Argument::cetera())->shouldNotBeCalled();

        $this->onKernelView($event);
    }

    function it_should_use_the_right_template_depending_on_request_format($request, $response, $reqManip, $engine, $twig, $event, $cnp, $mvh)
    {
        $reqManip->hasAttribute($request, '_controller')->willReturn(true);
        $reqManip->getAttribute($request, '_controller')->willReturn('App\Controller\CheeseController::eatAction');

        $request->getRequestFormat()->willReturn('xml');

        $twig->loadTemplate('App:Cheese:eat.xml.twig')->willReturn();
        $engine->renderResponse('App:Cheese:eat.xml.twig', array('foo' => 'bar'))->willReturn($response);

        $event->setResponse($response)->shouldBeCalled();
        $event->getRequest()->willReturn($request);
        $event->getControllerResult()->willReturn(array('foo' => 'bar'));

        $mvh->handleMissingView(\Prophecy\Argument::cetera())->shouldNotBeCalled();

        $this->onKernelView($event);
    }

    function it_should_abort_when_controller_is_not_in_request_attributes($reqManip, $request, $event)
    {
        $reqManip->hasAttribute($request, '_controller')->willReturn(false);

        $event->getControllerResult()->willReturn(array('foo' => 'bar'));
        $event->setResponse(\Prophecy\Argument::cetera())->shouldNotBeCalled();

        $this->onKernelView($event);
    }

    function it_should_forward_event_to_missing_view_handler_when_view_does_not_exist($request, $response, $reqManip, $engine, $twig, $event, $cnp, $mvh)
    {
        $reqManip->hasAttribute($request, '_controller')->willReturn(true);
        $reqManip->getAttribute($request, '_controller')->willReturn('App\Controller\CheeseController::eatAction');

        $request->getRequestFormat()->willReturn('html');

        $event->getControllerResult()->willReturn(array('foo' => 'bar'));

        $twig->loadTemplate('App:Cheese:eat.html.twig')->willThrow(new \Twig_Error_Loader('Unable to find template "App:Cheese:eat.html.twig".'));

        $mvh->handleMissingView($event, 'App:Cheese:eat.html.twig', array('foo' => 'bar'))->shouldBeCalled();

        $this->onKernelView($event);
    }

    function it_should_deduce_view_with_correct_bundle_name($request, $response, $reqManip, $engine, $twig, $event, $cnp, $mvh)
    {
        $this->beConstructedWith($engine, $twig, $cnp, 'twig', 'TestBundle', $mvh, $reqManip);

        $reqManip->hasAttribute($request, '_controller')->willReturn(true);
        $reqManip->getAttribute($request, '_controller')->willReturn('App\Controller\CheeseController::eatAction');

        $request->getRequestFormat()->willReturn('html');

        $event->getControllerResult()->willReturn(array('foo' => 'bar'));

        $twig->loadTemplate('TestBundle:Cheese:eat.html.twig')->willThrow(new \Twig_Error_Loader('Unable to find template "TestBundle:Cheese:eat.html.twig".'));
        $mvh->handleMissingView($event, 'TestBundle:Cheese:eat.html.twig', array('foo' => 'bar'))->shouldBeCalled();

        $this->onKernelView($event);
    }

    function it_should_abort_when_controller_is_not_within_the_App_bundle($reqManip, $request, $event)
    {
        $reqManip->hasAttribute($request, '_controller')->willReturn(true);
        $reqManip->getAttribute($request, '_controller')->willReturn('Some\Fancy\Controller\CheeseController::eatAction');

        $event->getControllerResult()->willReturn(array('foo' => 'bar'));
        $event->setResponse(\Prophecy\Argument::cetera())->shouldNotBeCalled();

        $this->onKernelView($event);
    }

    function it_should_not_handle_missing_view_if_template_exists_but_fails_to_load($request, $response, $reqManip, $engine, $twig, $event, $cnp, $mvh)
    {
        $reqManip->hasAttribute($request, '_controller')->willReturn(true);
        $reqManip->getAttribute($request, '_controller')->willReturn('App\Controller\CheeseController::eatAction');

        $request->getRequestFormat()->willReturn('html');

        $event->getControllerResult()->willReturn(array('foo' => 'bar'));

        $twig->loadTemplate('App:Cheese:eat.html.twig')->willThrow(new \Twig_Error_Loader('another unrelated error'));
        $mvh->handleMissingView(\Prophecy\Argument::cetera())->shouldNotBeCalled();

        $this->shouldThrow()->duringOnKernelView($event);
    }


}
