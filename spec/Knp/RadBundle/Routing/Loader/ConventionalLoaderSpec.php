<?php

namespace spec\Knp\RadBundle\Routing\Loader;

use PhpSpec\ObjectBehavior;
use PhpSpec\Exception\Example\PendingException;

use InvalidArgumentException;
use Symfony\Component\Routing\RouteCollection;
use Prophecy\Argument;

class ConventionalLoaderSpec extends ObjectBehavior
{
    /**
     * @param Symfony\Component\Config\FileLocatorInterface $locator
     * @param Knp\RadBundle\Routing\Loader\YamlParser       $yaml
     */
    function let($locator, $yaml)
    {
        $this->beConstructedWith($locator, $yaml);

        $locator->locate('routing.yml')->willReturn('yaml file');
        $yaml->parse('yaml file')->willReturn(array(
            'route_key' => array('route configuration')
        ));
    }

    function it_should_support_conventional_resources()
    {
        $this->supports('', 'rad_convention')->shouldReturn(true);
    }

    function it_should_not_support_other_resources()
    {
        $this->supports('')->shouldNotReturn(true);
    }

    /**
     * @param Knp\RadBundle\Routing\Resolver\RouteResolver $resolver
     */
    function its_load_should_use_resolver_and_return_route_collection($resolver, $yaml)
    {
        $this->setResolver($resolver);
        $resolver->resolve(Argument::cetera())->willReturn(new RouteCollection);

        $this->load('routing.yml')->shouldHaveType('Symfony\Component\Routing\RouteCollection');
    }

    function its_load_should_throw_an_exception_if_no_resolver_is_precised()
    {
        $this->shouldThrow('RuntimeException')->duringLoad('foo');
    }

    /**
     * @param Knp\RadBundle\Routing\Resolver\RouteResolver $resolver
     */
    function its_load_should_throw_an_exception_if_resolver_does_not_return_a_RouteCollection($resolver)
    {
        $this->setResolver($resolver);
        $resolver->resolve(Argument::cetera())->willReturn('bad value');

        $this->shouldThrow('RuntimeException')->duringLoad('routing.yml');
    }
}
