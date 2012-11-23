<?php

namespace spec\Knp\RadBundle\Resource\Resolver;

use PHPSpec2\ObjectBehavior;

class ArgumentResolver extends ObjectBehavior
{
    /**
     * @param Knp\RadBundle\HttpFoundation\RequestManipulator $requestManipulator
     * @param Symfony\Component\HttpFoundation\Request $request
     */
    function let($requestManipulator, $request)
    {
        $this->beConstructedWith($requestManipulator);
    }

    function it_should_resolve_scalar_arguments($request)
    {
        $this->resolveArgument($request, array('value' => 'the value'))->shouldReturn('the value');
    }

    function it_should_resolve_path_arguments($requestManipulator, $request)
    {
        $requestManipulator->getAttribute($request, 'slug')->shouldBeCalled()->willReturn('mimolette');

        $this->resolveArgument($request, array('name' => 'slug'))->shouldReturn('mimolette');
    }

    function it_should_throw_exception_when_an_argument_has_both_value_and_name($request)
    {
        $this->shouldThrow()->duringResolveArgument($request, array('name' => 'slug', 'value' => 'camember'));
    }

    function it_should_throw_exception_when_an_argument_has_no_value_nor_name($request)
    {
        $this->shouldThrow()->duringResolveArgument($request, array());
    }

    function it_should_throw_exception_when_an_argument_has_extra_keys($request)
    {
        $this->shouldThrow()->duringResolveArgument($request, array('name' => 'slug', 'foo' => 'bar'));
    }
}
