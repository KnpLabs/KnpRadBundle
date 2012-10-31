<?php

namespace Knp\RadBundle\Resource\Resolver;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResourceResolver
{
    private $argumentResolver;
    private $optionsResolver;

    public function __construct(ContainerInterface $container, ArgumentResolver $argumentResolver = null)
    {
        $this->container = $container;
        $this->argumentResolver = $argumentResolver ?: new ArgumentResolver();

        $this->optionsResolver = new OptionsResolver();
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

        $service = $this->container->get($options['service']);

        return call_user_func_array(array($service, $options['method']), $arguments);
    }
}
