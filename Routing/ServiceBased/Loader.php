<?php

namespace Knp\RadBundle\Routing\ServiceBased;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

class Loader implements LoaderInterface
{
    private $resolver;
    private $tags;
    private $arrayConfigFactory;
    private $expressionFactory;
    private $yamlFactory;

    public function __construct(array $tags, Factory\Expression $expressionFactory = null, Factory\ArrayConfig $arrayConfigFactory = null, Factory\Yaml $yamlFactory = null)
    {
        $this->tags = $tags;
        $this->arrayConfigFactory = $arrayConfigFactory ?: new Factory\ArrayConfig;
        $this->expressionFactory = $expressionFactory ?: new Factory\Expression($this->arrayConfigFactory);
        $this->yamlFactory = $yamlFactory ?: new Factory\Yaml($this->arrayConfigFactory);
    }

    public function load($class, $type = null)
    {
        $routes = new RouteCollection;
        foreach ($this->tags as $id => $tag) {
            $routes->add($id, $this->createRoute($id, $tag));
        }

        return $routes;
    }

    private function createRoute($id, array $tag)
    {
        if (isset($tag['expr'])) {
            return $this->expressionFactory->create($id, $tag['expr']);
        }
        if (isset($tag['yaml'])) {
            return $this->yamlFactory->create($id, $tag['yaml']);
        }

        return $this->arrayConfigFactory->create($id, $tag);
    }

    public function getResolver()
    {
        return $this->ressolver;
    }

    public function supports($resource, $type = null)
    {
        return true;
    }

    public function setResolver(LoaderResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }
}
