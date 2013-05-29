<?php

namespace spec\Knp\RadBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionRethrowListenerSpec extends ObjectBehavior
{
    /**
     * @param Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent $event
     */
    function its_onKernelException_should_rethrow_non_HttpException($event)
    {
        $exception = new \Exception;
        $event->getException()->willReturn($exception);

        $this->shouldThrow($exception)->duringOnKernelException($event);
    }

    /**
     * @param Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent $event
     */
    function its_onKernelException_should_not_rethrow_HttpException($event)
    {
        $exception = new NotFoundHttpException;
        $event->getException()->willReturn($exception);

        $this->shouldNotThrow()->duringOnKernelException($event);
    }
}
