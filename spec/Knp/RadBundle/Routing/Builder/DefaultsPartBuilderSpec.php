<?php

namespace spec\Knp\RadBundle\Routing\Builder;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Routing\Route;

class DefaultsPartBuilderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Knp\RadBundle\Routing\Builder\DefaultsPartBuilder');
    }

    function it_should_be_a_valid_part_builder()
    {
        $this->shouldHaveType('Knp\RadBundle\Routing\Builder\RoutePartBuilderInterface');
    }

    function its_build_should_set_up_the_defaults_route_key()
    {
        $this
            ->build(new Route(null), 'base_name', 'App:Test', 'action', array(
                'defaults' => array(
                    'foo' => 'bar'
                )
            ))
            ->getDefaults()
            ->shouldReturn(array('foo' => 'bar'))
       ;
    }

    function its_build_should_defined_defalts_route_key_with_parent_value()
    {
        $parent = new Route(null);
        $parent->setDefaults(array(
            'foo' => 'baz',
            'bar' => 'foo',
        ));
        $this
            ->build(new Route(null), 'base_name', 'App:Test', 'action', array(
                'defaults' => array(
                    'foo' => 'bar'
                )
            ), array('action' => $parent))
            ->getDefaults()
            ->shouldReturn(array('foo' => 'bar', 'bar' => 'foo'))
       ;
    }
}
