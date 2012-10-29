<?php

namespace Knp\RadBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

/**
 * Adds Response event listener to render no-Response
 * controller results (arrays).
 */
class ViewListener
{
    private $templating;
    private $engine;

    /**
     * Initializes listener.
     *
     * @param EngineInterface $templating  Templating engine
     * @param string          $engine      Default engine name
     */
    public function __construct(EngineInterface $templating, $engine)
    {
        $this->templating = $templating;
        $this->engine     = $engine;
    }

    /**
     * Patches response on empty responses.
     *
     * @param GetResponseForControllerResultEvent $event Event instance
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request    = $event->getRequest();
        $attributes = $request->attributes;

        if (false === strpos($attributes->get('_controller'), '::')) {
            return;
        }

        list($class, $method) = explode('::', $attributes->get('_controller'));

        $group = preg_replace(array('#^.*\\Controller\\\\#', '#Controller$#'), '', $class);
        $group = str_replace('\\', '/', $group);
        $view  = preg_replace('/Action$/', '', $method);

        $event->setResponse($this->templating->renderResponse(
            sprintf('App:%s:%s.%s.%s', $group, $view, $request->getRequestFormat(), $this->engine),
            $event->getControllerResult()
        ));
    }
}
