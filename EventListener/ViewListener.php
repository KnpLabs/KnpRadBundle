<?php

namespace Knp\RadBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser;
use Knp\RadBundle\HttpFoundation\RequestManipulator;
use Knp\RadBundle\AppBundle\BundleGuesser;

/**
 * Adds Response event listener to render no-Response
 * controller results (arrays).
 */
class ViewListener
{
    private $templating;
    private $twigEnvironment;
    private $parser;
    private $engine;
    private $requestManipulator;
    private $bundleGuesser;

    /**
     * Initializes listener.
     *
     * @param EngineInterface      $templating         Templating engine
     * @param ControllerNameParser $parser             Controller name parser
     * @param string               $engine             Default engine name
     * @param BundleGuesser        $bundleGuesser      To guess the current rad bundle
     * @param MissingViewHandler   $missingViewHandler The handle to be used in case the view does not exist
     * @param RequestManipulator   $requestManipulator The request manipulator
     */
    public function __construct(EngineInterface $templating, \Twig_Environment $twigEnvironment, ControllerNameParser $parser, $engine, BundleGuesser $bundleGuesser, MissingViewHandler $missingViewHandler = null, RequestManipulator $requestManipulator = null)
    {
        $this->templating         = $templating;
        $this->twigEnvironment    = $twigEnvironment;
        $this->parser             = $parser;
        $this->engine             = $engine;
        $this->bundleGuesser      = $bundleGuesser;
        $this->missingViewHandler = $missingViewHandler ?: new MissingViewHandler();
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
        if (!$this->bundleGuesser->hasBundleForClass($class)) {
            return;
        }

        $viewName   = $this->deduceViewName($class, $method, $request->getRequestFormat());
        $viewParams = $event->getControllerResult() ?: array();

        if ($this->templateExists($viewName)) {
            $response = $this->templating->renderResponse($viewName, $viewParams);
            $event->setResponse($response);
        } else {
            $this->missingViewHandler->handleMissingView($event, $viewName, $viewParams);
        }
    }

    private function deduceViewName($class, $method, $format)
    {
        $group = preg_replace(array('#^.*\\Controller\\\\#', '#Controller$#'), '', $class);
        $group = str_replace('\\', '/', $group);
        $view  = preg_replace('/Action$/', '', $method);
        $bundle = $this->bundleGuesser->getBundleForClass($class);

        return sprintf('%s:%s:%s.%s.%s', $bundle->getName(), $group, $view, $format, $this->engine);
    }

    private function templateExists($template)
    {
        $loader = $this->twigEnvironment->getLoader();
        if ($loader instanceof \Twig_ExistsLoaderInterface) {
            return $loader->exists($template);
        }

        try {
            $loader->getSource($template);

            return true;
        } catch (\Twig_Error_Loader $e) {
            throw $e;
        }

        return false;
    }
}
