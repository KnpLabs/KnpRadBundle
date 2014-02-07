<?php

namespace Knp\RadBundle\Resource\Resolver;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface ResourceResolver
{
    public function resolveResource(Request $request, array $options);
}
