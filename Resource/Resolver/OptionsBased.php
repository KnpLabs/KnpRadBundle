<?php

namespace Knp\RadBundle\Resource\Resolver;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Knp\RadBundle\Resource\Resolver\ResourceResolver;
use Knp\RadBundle\Resource\Resolver\OptionsBased\ArgumentResolver;

class OptionsBased implements ResourceResolver
{
    private $argumentResolver;
    private $optionsResolver;

    public function __construct(ContainerInterface $container, ArgumentResolver $argumentResolver = null)
    {
        $this->container = $container;
        $this->argumentResolver = $argumentResolver ?: new ArgumentResolver;

        $this->optionsResolver = new OptionsResolver;
        $this->optionsResolver->setRequired(array('service', 'method'));
        $this->optionsResolver->setDefaults(array('arguments' => array()));
        $this->optionsResolver->setAllowedTypes(array(
            'service'   => 'string',
            'method'    => 'string',
            'arguments' => 'array',
        ));
    }

    public function resolveResource(Request $request, array $options)
    {
        $options = $this->optionsResolver->resolve($options);

        $arguments = array();
        foreach ($options['arguments'] as $i => $argument) {
            if (is_string($argument)) {
                $arguments[] = $this->argumentResolver->resolveName($request, $argument);
            } else {
                try {
                    $arguments[] = $this->argumentResolver->resolveArgument($request, $argument);
                } catch (\Exception $e) {
                    throw new \RuntimeException(
                        sprintf('Failed to resolve resource argument[%s].', $i),
                        0,
                        $e
                    );
                }
            }
        }

        $service  = $this->container->get($options['service']);
        $resolved = call_user_func_array(array($service, $options['method']), $arguments);

        if (null === $resolved) {
            throw new ResolutionFailureException('The resolution resulted in a NULL value.');
        }

        return $resolved;
    }
}
