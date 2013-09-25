<?php

namespace Knp\RadBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Knp\RadBundle\Resource\Resolver\RequestResolver;
use Knp\RadBundle\Resource\Resolver\ResolutionFailureException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Knp\RadBundle\Reflection\ReflectionFactory;

class ResourceResolverListener
{
    private $requestResolver;
    private $reflectionFactory;

    public function __construct(RequestResolver $requestResolver, ReflectionFactory $reflectionFactory)
    {
        $this->requestResolver = $requestResolver;
        $this->reflectionFactory = $reflectionFactory;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();
        $controller = $event->getController();


        foreach ($this->getParameters($controller) as $param) {
            $name = $param->getName();
            if (!$this->requestResolver->hasResourceOptions($request, $name)) {
                continue;
            }
            try {
                $this->requestResolver->resolveResource($request, $name);
            } catch (ResolutionFailureException $e) {
                if ($param->isOptional()) {
                    continue;
                }
                throw new NotFoundHttpException('Unable to resolve resources.', $e);
            }
        }
    }

    private function getParameters($controller)
    {
        return $this->reflectionFactory->getParameters($controller);
    }
}
