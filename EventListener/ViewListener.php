<?php

namespace Knp\RadBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser;
use Knp\RadBundle\HttpFoundation\RequestManipulator;

/**
 * Adds Response event listener to render no-Response
 * controller results (arrays).
 */
class ViewListener
{
    private $templating;
    private $parser;
    private $engine;
    private $requestManipulator;

    /**
     * Initializes listener.
     *
     * @param EngineInterface      $templating         Templating engine
     * @param ControllerNameParser $parser             Controller name parser
     * @param string               $engine             Default engine name
     * @param RequestManipulator   $requestManipulator The request manipulator
     */
    public function __construct(EngineInterface $templating, ControllerNameParser $parser, $engine, RequestManipulator $requestManipulator = null)
    {
        $this->templating         = $templating;
        $this->parser             = $parser;
        $this->engine             = $engine;
        $this->requestManipulator = $requestManipulator ?: new RequestManipulator();
    }

    /**
     * Patches response on empty responses.
     *
     * @param GetResponseForControllerResultEvent $event Event instance
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();

        if (false === $this->requestManipulator->hasAttribute($request, '_controller')) {
            return;
        }

        $controller = $this->requestManipulator->getAttribute($request, '_controller');
        if (false === strpos($controller, '::')) {
            $controller = $this->parser->parse($controller);
        }

        list($class, $method) = explode('::', $controller, 2);

        $group = preg_replace(array('#^.*\\Controller\\\\#', '#Controller$#'), '', $class);
        $group = str_replace('\\', '/', $group);
        $view  = preg_replace('/Action$/', '', $method);

        $event->setResponse($this->templating->renderResponse(
            sprintf('App:%s:%s.%s.%s', $group, $view, $request->getRequestFormat(), $this->engine),
            $event->getControllerResult()

        ));
    }
}
