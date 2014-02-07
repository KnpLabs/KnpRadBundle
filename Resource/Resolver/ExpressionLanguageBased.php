<?php

namespace Knp\RadBundle\Resource\Resolver;

use Knp\RadBundle\Resource\Resolver\ResourceResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ExpressionLanguage;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ExpressionLanguageBased implements ResourceResolver
{
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function resolveResource(Request $request, array $options)
    {
        $expression = new ExpressionLanguage;

        return $expression->evaluate($options['expr'], array(
            'request' => $request,
            'container' => $this->container,
        ));
    }
}
