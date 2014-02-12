<?php

namespace spec\Knp\RadBundle\EventListener;

use PhpSpec\ObjectBehavior;

class ViewListenerSpec extends ObjectBehavior
{
    /**
     * @param Symfony\Component\HttpFoundation\Request                               $request
     * @param Symfony\Component\HttpFoundation\ParameterBag                          $params
     * @param Symfony\Component\HttpFoundation\Response                              $response
     * @param Symfony\Bundle\FrameworkBundle\Templating\EngineInterface              $engine
     * @param Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent $event
     * @param Knp\RadBundle\EventListener\MissingViewHandler                         $mvh
     * @param Knp\RadBundle\View\NameDeducer                                         $deducer
     */
    function let($request, $params, $engine, $event, $mvh, $deducer)
    {
        $event->getRequest()->willReturn($request);
        $request->attributes = $params;
        $this->beConstructedWith($engine, $deducer, $mvh);
    }

    function it_should_create_a_view_response_when_controller_did_not_return_any($deducer, $request, $params, $response, $engine, $event, $mvh)
    {
        $params->get('_controller')->willReturn('App\Controller\CheeseController::eatAction');
        $deducer->deduce($request)->willReturn('App:Cheese:eat.html.twig');
        $engine->exists('App:Cheese:eat.html.twig')->willReturn(true);
        $engine->renderResponse('App:Cheese:eat.html.twig', array('foo' => 'bar'))->willReturn($response);

        $event->setResponse($response)->shouldBeCalled();
        $event->getRequest()->willReturn($request);
        $event->getControllerResult()->willReturn(array('foo' => 'bar'));

        $mvh->handleMissingView(\Prophecy\Argument::cetera())->shouldNotBeCalled();

        $this->onKernelView($event);
    }

    function it_should_create_a_view_response_when_controller_return_null($deducer, $request, $params, $response, $engine, $event, $mvh)
    {
        $params->get('_controller')->willReturn('App\Controller\CheeseController::eatAction');
        $deducer->deduce($request)->willReturn('App:Cheese:eat.html.twig');
        $engine->exists('App:Cheese:eat.html.twig')->willReturn(true);
        $engine->renderResponse('App:Cheese:eat.html.twig', array())->willReturn($response);

        $event->setResponse($response)->shouldBeCalled();
        $event->getRequest()->willReturn($request);
        $event->getControllerResult()->willReturn(null);

        $mvh->handleMissingView(\Prophecy\Argument::cetera())->shouldNotBeCalled();

        $this->onKernelView($event);
    }

    function it_should_resolve_controller_when_not_yet_resolved($deducer, $request, $params, $response, $engine, $event, $mvh)
    {
        $params->get('_controller')->willReturn('App\Controller\CheeseController::eatAction');
        $deducer->deduce($request)->willReturn('App:Cheese:eat.html.twig');
        $engine->exists('App:Cheese:eat.html.twig')->willReturn(true);
        $engine->renderResponse('App:Cheese:eat.html.twig', array('foo' => 'bar'))->willReturn($response);

        $event->setResponse($response)->shouldBeCalled();
        $event->getRequest()->willReturn($request);
        $event->getControllerResult()->willReturn(array('foo' => 'bar'));

        $mvh->handleMissingView(\Prophecy\Argument::cetera())->shouldNotBeCalled();

        $this->onKernelView($event);
    }

    function it_should_use_the_right_template_depending_on_request_format($deducer, $request, $params, $response, $engine, $event, $mvh)
    {
        $params->get('_controller')->willReturn('App\Controller\CheeseController::eatAction');
        $deducer->deduce($request)->willReturn('App:Cheese:eat.xml.twig');
        $engine->exists('App:Cheese:eat.xml.twig')->willReturn(true);
        $engine->renderResponse('App:Cheese:eat.xml.twig', array('foo' => 'bar'))->willReturn($response);

        $event->setResponse($response)->shouldBeCalled();
        $event->getRequest()->willReturn($request);
        $event->getControllerResult()->willReturn(array('foo' => 'bar'));

        $mvh->handleMissingView(\Prophecy\Argument::cetera())->shouldNotBeCalled();

        $this->onKernelView($event);
    }

    function it_should_abort_when_controller_is_not_in_request_attributes($deducer, $request, $params, $event)
    {
        $event->getControllerResult()->willReturn(array('foo' => 'bar'));
        $event->setResponse(\Prophecy\Argument::cetera())->shouldNotBeCalled();

        $this->onKernelView($event);
    }

    function it_should_forward_event_to_missing_view_handler_when_view_does_not_exist($deducer, $request, $params, $response, $engine, $event, $mvh)
    {
        $params->get('_controller')->willReturn('App\Controller\CheeseController::eatAction');
        $deducer->deduce($request)->willReturn('App:Cheese:eat.html.twig');
        $engine->exists('App:Cheese:eat.html.twig')->willReturn(false);
        $event->getControllerResult()->willReturn(array('foo' => 'bar'));
        $mvh->handleMissingView($event, 'App:Cheese:eat.html.twig', array('foo' => 'bar'))->shouldBeCalled();

        $this->onKernelView($event);
    }

    function it_should_deduce_view_with_correct_bundle_name($deducer, $request, $params, $response, $engine, $event, $mvh)
    {
        $params->get('_controller')->willReturn('TestBundle\Controller\CheeseController::eatAction');
        $deducer->deduce($request)->willReturn('TestBundle:Cheese:eat.html.twig');
        $engine->exists('TestBundle:Cheese:eat.html.twig')->willReturn(false);
        $event->getControllerResult()->willReturn(array('foo' => 'bar'));
        $mvh->handleMissingView($event, 'TestBundle:Cheese:eat.html.twig', array('foo' => 'bar'))->shouldBeCalled();

        $this->onKernelView($event);
    }

    function it_should_abort_when_controller_is_not_within_the_App_bundle($deducer, $request, $params, $event)
    {
        $event->getControllerResult()->willReturn(array('foo' => 'bar'));
        $event->setResponse(\Prophecy\Argument::cetera())->shouldNotBeCalled();

        $this->onKernelView($event);
    }

    /**
     * @param Twig_LoaderInterface $tli
     */
    function it_should_not_handle_missing_view_if_template_exists_but_fails_to_load($deducer, $request, $params, $response, $engine, $event, $mvh)
    {
        $request->getRequestFormat()->willReturn('html');
        $event->getControllerResult()->willReturn(array('foo' => 'bar'));
        $engine->renderResponse(\Prophecy\Argument::any())->willReturn(new \Twig_Error_Loader('fail!'));

        $mvh->handleMissingView(\Prophecy\Argument::cetera())->shouldNotBeCalled();

        $this->shouldThrow()->duringOnKernelView($event);
    }
}
