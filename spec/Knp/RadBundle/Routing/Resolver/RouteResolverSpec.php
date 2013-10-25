<?php

namespace spec\Knp\RadBundle\Routing\Resolver;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Routing\RouteCollection;

class RouteResolverSpec extends ObjectBehavior
{
    function it_should_be_a_RouteResolverCollection()
    {
        $this->shouldHaveType('Knp\RadBundle\Routing\Resolver\RouteResolver');
    }

    function it_should_be_a_valid_resolver()
    {
        $this->shouldHaveType('Symfony\Component\Config\Loader\LoaderResolverInterface');
    }

    /**
     * @param Symfony\Component\Config\Loader\LoaderInterface $loader
     */
    function its_resolve_should_resolve_a_set_of_key_and_configuration($loader)
    {
        $this->add($loader);
        $loader->supports(Argument::cetera())->willReturn(true);
        $loader->load(Argument::cetera())->willReturn(new RouteCollection);

        $this
            ->resolve(array('route configuration'), 'some_route_key')
            ->shouldHaveType('Symfony\Component\Routing\RouteCollection')
        ;
    }

    function its_resolve_should_return_throw_an_exception_if_no_loader_match()
    {
        $this
            ->shouldThrow('Symfony\Component\Routing\Exception\RouteNotFoundException')
            ->duringResolve('foo', 'bar')
        ;
    }
}
