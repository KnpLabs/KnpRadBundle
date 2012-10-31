<?php

namespace Knp\RadBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser;

/**
 * Adds Response event listener to render no-Response
 * controller results (arrays).
 */
class ViewListener
{
    private $templating;
    private $parser;
    private $engine;

    /**
     * Initializes listener.
     *
     * @param EngineInterface      $templating  Templating engine
     * @param ControllerNameParser $parser      Controller name parser
     * @param string               $engine      Default engine name
     */
    public function __construct(EngineInterface $templating, ControllerNameParser $parser, $engine)
    {
        $this->templating = $templating;
        $this->parser     = $parser;
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

        if (!$attributes->has('_controller')) {
            return;
        }

        $controller = $attributes->get('_controller');
        if (3 == count(explode(':', $controller))) {
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
