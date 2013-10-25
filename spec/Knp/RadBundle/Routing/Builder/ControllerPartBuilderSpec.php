<?php

namespace spec\Knp\RadBundle\Routing\Builder;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Routing\Route;

class ControllerPartBuilderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Knp\RadBundle\Routing\Builder\ControllerPartBuilder');
    }

    function it_should_be_a_valid_part_builder()
    {
        $this->shouldHaveType('Knp\RadBundle\Routing\Builder\RoutePartBuilderInterface');
    }

    function its_build_should_defined_a_controller_with_the_resource_and_the_action()
    {
        $this
            ->build(new Route(null), 'base_name', 'App:Test', 'action')
            ->getDefaults()
            ->shouldReturn(array('_controller' => 'App:Test:action'))
        ;
    }

    function its_build_should_take_default_controller_if_exists()
    {
        $this
            ->build(new Route(null), 'base_name', 'App:Test', 'action', array(
                'defaults' => array(
                    '_controller' => 'Foo:Bar:baz'
                )
            ))
            ->getDefaults()
            ->shouldReturn(array('_controller' => 'Foo:Bar:baz'))
        ;
    }
}
