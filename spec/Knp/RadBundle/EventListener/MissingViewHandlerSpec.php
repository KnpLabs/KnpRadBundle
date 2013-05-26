<?php

namespace spec\Knp\RadBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class MissingViewHandlerSpec extends ObjectBehavior
{
    /**
     * @param  Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     * @param  Symfony\Component\HttpFoundation\Request $request
     * @param  Symfony\Component\HttpFoundation\Response $response
     * @param  Symfony\Component\HttpKernel\HttpKernel $kernel
     */
    function let($event, $request, $response, $kernel)
    {
        $event->getKernel()->willReturn($kernel);
        $event->getRequest()->willReturn($request);
    }

    /**
     * @param Symfony\Component\HttpFoundation\Request $subRequest
     **/
    function it_should_create_missing_view_responses($kernel, $event, $request, $subRequest, $response)
    {
        $viewName   = 'App:Cheese:missingView.html.twig';
        $viewParams = array('some' => 'view params');

        $request->duplicate(array(), null, array(
            '_controller' => 'KnpRadBundle:Assistant:missingView',
            'viewName'    => $viewName,
            'viewParams'  => $viewParams
        ))->willReturn($subRequest);

        $kernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST)->willReturn($response);

        $event->setResponse($response)->shouldBeCalled();

        $this->handleMissingView($event, $viewName, $viewParams);
    }
}
