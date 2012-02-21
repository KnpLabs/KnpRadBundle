<?php

/*
 * This file is part of the KnpRadBundle package.
 *
 * (c) KnpLabs <http://knplabs.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Knp\Bundle\RadBundle\EventListener;

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
    private $watchedNamespace;
    private $templating;
    private $engine;

    /**
     * Initializes listener.
     *
     * @param string          $projectName Project namespace
     * @param EngineInterface $templating  Templating engine
     * @param string          $engine      Default engine name
     */
    public function __construct($projectName, EngineInterface $templating, $engine)
    {
        $this->watchedNamespace = $projectName.'\\Controller';
        $this->templating       = $templating;
        $this->engine           = $engine;
    }

    /**
     * Patches response on empty responses.
     *
     * @param GetResponseForControllerResultEvent $event Event instance
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();

        $attributes = $request->attributes;
        if (false === strpos($attributes->get('_controller'), '::')) {
            return;
        }

        list($class, $method) = explode('::', $attributes->get('_controller'));
        if ($this->watchedNamespace !== substr($class, 0, strlen($this->watchedNamespace))) {
            return;
        }

        $group = str_replace('\\', '/', substr($class, strlen($this->watchedNamespace) + 1, -10));
        $view  = preg_replace('/Action$/', '', $method);

        $event->setResponse($this->templating->renderResponse(
            sprintf('App:%s:%s.%s.%s', $group, $view, $request->getRequestFormat(), $this->engine),
            $event->getControllerResult()
        ));
    }
}
