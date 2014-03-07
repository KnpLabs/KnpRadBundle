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
    private $templating;
    private $parser;
    private $bundleGuesser;
    private $requestManipulator;
    private $engine;

    public function __construct(ContainerInterface $container, EngineInterface $templating, ControllerNameParser $parser, BundleGuesser $bundleGuesser, RequestManipulator $requestManipulator = null, $engine = 'twig')
    {
        $this->container = $container;
        $this->templating = $templating;
        $this->parser = $parser;
        $this->bundleGuesser = $bundleGuesser;
        $this->requestManipulator = $requestManipulator ?: new RequestManipulator;
        $this->engine = $engine;
    }

    public function deduce(Request $request)
    {
        if ($this->requestManipulator->hasAttribute($request, 'view')) {
            $view = $this->requestManipulator->getAttribute($request, 'view');

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
