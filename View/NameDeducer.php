<?php

namespace Knp\RadBundle\View;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser;
use Symfony\Component\Templating\EngineInterface;
use Knp\RadBundle\HttpFoundation\RequestManipulator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Knp\RadBundle\AppBundle\BundleGuesser;

class NameDeducer
{
    private $container;
    private $requestManipulator;
    private $templating;
    private $bundleGuesser;
    private $parser;
    private $engine;

    public function __construct(ContainerInterface $container, RequestManipulator $requestManipulator, EngineInterface $templating, ControllerNameParser $parser, BundleGuesser $bundleGuesser, $engine)
    {
        $this->container = $container;
        $this->requestManipulator = $requestManipulator;
        $this->templating = $templating;
        $this->bundleGuesser = $bundleGuesser;
        $this->parser = $parser;
        $this->engine = $engine;
    }

    public function deduce(Request $request)
    {
        if ($this->requestManipulator->hasAttribute($request, '_view')) {
            $view = $this->requestManipulator->getAttribute($request, '_view');

            return sprintf('%s.%s.%s', $view, $request->getRequestFormat(), $this->engine);
        }

        return $this->deduceViewName($request);
    }


    private function deduceViewName(Request $request)
    {
        if (false === $this->requestManipulator->hasAttribute($request, '_controller')) {
            throw new NoControllerNameException;
        }

        $controller = $this->requestManipulator->getAttribute($request, '_controller');

        if (2 === count($exploded = explode(':', $controller))) {
            return $this->getName($request, $this->getNormalizedNameFromService($controller));
        }

        if (false === strpos($controller, '::')) {
            return $this->getName($request, $this->parser->parse($controller));
        }

        return $this->getName($request, $controller);
    }

    private function getName(Request $request, $controller)
    {
        list($class, $method) = explode('::', $controller, 2);
        if (!$this->bundleGuesser->hasBundleForClass($class)) {
            throw new NotInBundleException($class);
        }

        $group = preg_replace(array('#^.*\\Controller\\\\#', '#Controller$#'), '', $class);
        $group = str_replace('\\', '/', $group);
        $view  = preg_replace('/Action$/', '', $method);
        $bundle = $this->bundleGuesser->getBundleForClass($class);

        return sprintf('%s:%s:%s.%s.%s', $bundle->getName(), $group, $view, $request->getRequestFormat(), $this->engine);
    }

    private function getNormalizedNameFromService($controller)
    {
        list($service, $method) = explode(':', $controller);
        $class = get_class($this->container->get($service));

        return sprintf('%s::%s', $class, $method);
    }

}
