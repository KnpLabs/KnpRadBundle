<?php

namespace Knp\RadBundle\Resource\Resolver;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Knp\RadBundle\HttpFoundation\RequestManipulator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArgumentResolver
{
    private $requestManipulator;
    private $optionsResolver;

    public function __construct(RequestManipulator $requestManipulator = null)
    {
        $this->requestManipulator = $requestManipulator ?: new RequestManipulator();

        $this->optionsResolver = new OptionsResolver();
        $this->optionsResolver->setOptional(array('name', 'value'));
        $this->optionsResolver->setAllowedTypes(array('name' => 'string', 'value' => 'string'));
    }

    public function resolveArgument(Request $request, array $options)
    {
        $options = $this->optionsResolver->resolve($options);

        $hasName = array_key_exists('name', $options);
        $hasValue = array_key_exists('value', $options);

        if ($hasName && $hasValue) {
            throw new \InvalidArgumentException(
                'You must pass either a `name` or a `value` option, but not both.'
            );
        }

        if ($hasName) {
            return $this->requestManipulator->getAttribute($request, $options['name']);
        }

        if ($hasValue) {
            return $options['value'];
        }

        throw new \InvalidArgumentException(
            'You must path either a `name` or a `value` option.'
        );
    }
}
