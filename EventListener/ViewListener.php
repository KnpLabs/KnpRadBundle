<?php

namespace Knp\RadBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpFoundation\Request;
use Knp\RadBundle\View\NameDeducer;
use Knp\RadBundle\View\NameDeducer\NotInBundleException;
use Knp\RadBundle\View\NameDeducer\NoControllerNameException;
use Knp\RadBundle\EventListener\MissingViewHandler;
use Symfony\Component\Templating\EngineInterface;

/**
 * Adds Response event listener to render no-Response
 * controller results (arrays).
 */
class ViewListener
{
    private $templating;
    private $missingViewHandler;
    private $viewNameDeducer;

    /**
     * Initializes listener.
     *
     * @param EngineInterface      $templating         Templating engine
     * @param ViewNameDeducer      $viewNameDeducer    Deduces the view name from controller name
     * @param MissingViewHandler   $missingViewHandler handles missing views
     */
    public function __construct(EngineInterface $templating, NameDeducer $viewNameDeducer, MissingViewHandler $missingViewHandler)
    {
        $this->templating = $templating;
        $this->viewNameDeducer = $viewNameDeducer;
        $this->missingViewHandler = $missingViewHandler ?: new MissingViewHandler;
    }

    /**
     * Patches response on empty responses.
     *
     * @param GetResponseForControllerResultEvent $event Event instance
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();

        try {
            $viewName = $this->viewNameDeducer->deduce($request);
        }
        catch (NotInBundleException $e) {
            return;
        }
        catch (NoControllerNameException $e) {
            return;
        }
        $viewParams = $event->getControllerResult() ?: array();

        if ($this->templating->exists($viewName)) {
            $response = $this->templating->renderResponse($viewName, (array)$viewParams);
            $event->setResponse($response);
            return;
        }

        $this->missingViewHandler->handleMissingView($event, $viewName, (array)$viewParams);
    }
}
