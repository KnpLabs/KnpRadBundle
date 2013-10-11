<?php

namespace spec\Knp\RadBundle\Routing\Builder;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Routing\Route;

class RequirementsPartBuilderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Knp\RadBundle\Routing\Builder\RequirementsPartBuilder');
    }

    function it_should_be_a_valid_part_builder()
    {
        $this->shouldHaveType('Knp\RadBundle\Routing\Builder\RoutePartBuilderInterface');
    }

    function its_build_should_set_up_requirements()
    {
        $this
            ->build(new Route(null), 'base_name', 'App:Test', 'action', array(
                'requirements' => array(
                    'foo' => '\d+',
                )
            ))
            ->getRequirements()
            ->shouldReturn(array('foo' => '\d+'))
        ;
    }

    function its_build_should_set_up_requirements_with_parent()
    {
        $this
            ->build(new Route(null), 'base_name', 'App:Test', 'action', array(
                'requirements' => array(
                    'foo' => '\d+',
                )
            ), array(
                'requirements' => array(
                    'bar' => '\w+',
                    'foo' => '[A-Z]+',
                )
            ))
            ->getRequirements()
            ->shouldReturn(array('bar' => '\w+', 'foo' => '\d+'))
        ;
    }
}
