<?php

namespace spec\Knp\RadBundle\EventListener;

use PhpSpec\ObjectBehavior;

class MissingViewHandlerSpec extends ObjectBehavior
{
    /**
     * @param  Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     * @param  Symfony\Component\HttpFoundation\Response $response
     * @param  Symfony\Bundle\FrameworkBundle\HttpKernel $kernel
     */
    function let($event, $response, $kernel)
    {
        $event->getKernel()->willReturn($kernel);
    }

    function it_should_create_missing_view_responses($kernel, $event, $response)
    {
        $viewName   = 'App:Cheese:missingView.html.twig';
        $viewParams = array('some' => 'view params');

        $kernel->forward('KnpRadBundle:Assistant:missingView', array(
            'viewName'   => $viewName,
            'viewParams' => $viewParams
        ))->willReturn($response);


        $event->setResponse($response)->shouldBeCalled();

        $this->handleMissingView($event, $viewName, $viewParams);
    }
}
