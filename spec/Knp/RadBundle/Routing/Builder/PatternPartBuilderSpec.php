<?php

namespace spec\Knp\RadBundle\Routing\Builder;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Routing\Route;

class PatternPartBuilderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Knp\RadBundle\Routing\Builder\PatternPartBuilder');
    }

    function it_should_be_a_valid_route_part_builder()
    {
        $this->shouldHaveType('Knp\RadBundle\Routing\Builder\RoutePartBuilderInterface');
    }

    function its_build_should_take_the_defined_pattern_and_return_a_route()
    {
        $this
            ->build(new Route(null), 'base_name', 'App:Resource', 'action', array(
                'pattern' => '/patterns'
            ))
            ->getPath()
            ->shouldReturn('/patterns')
        ;
    }

    function its_build_should_guess_the_patterns_from_the_resource_if_patterns_is_not_defined()
    {
        $this
            ->build(new Route(null), 'base_name', 'App:Namespace\\SubNamespace/Resource', 'action')
            ->getPath()
            ->shouldReturn('/namespaces/sub_namespaces/resources/action')
        ;
    }

    function its_build_should_guess_defaults_actions_patterns()
    {
        $this
            ->build(new Route(null), 'base_name', 'App:Test', 'index')
            ->getPath()
            ->shouldReturn('/tests')
        ;
        $this
            ->build(new Route(null), 'base_name', 'App:Test', 'new')
            ->getPath()
            ->shouldReturn('/tests/new')
        ;
        $this
            ->build(new Route(null), 'base_name', 'App:Test', 'create')
            ->getPath()
            ->shouldReturn('/tests')
        ;
        $this
            ->build(new Route(null), 'base_name', 'App:Test', 'show')
            ->getPath()
            ->shouldReturn('/tests/{TestId}')
        ;
        $this
            ->build(new Route(null), 'base_name', 'App:Test', 'edit')
            ->getPath()
            ->shouldReturn('/tests/{TestId}/edit')
        ;
        $this
            ->build(new Route(null), 'base_name', 'App:Test', 'update')
            ->getPath()
            ->shouldReturn('/tests/{TestId}')
        ;
        $this
            ->build(new Route(null), 'base_name', 'App:Test', 'delete')
            ->getPath()
            ->shouldReturn('/tests/{TestId}')
        ;
    }

    function its_build_should_add_pattern_to_a_given_parent_pattern()
    {
        $parent = new Route(null);
        $parent->setPattern('/parent');
        $this
            ->build(new Route(null), 'base_name', 'App:Test', 'action', array(
                'pattern' => '/actions'
            ), $parent)
            ->getPath()
            ->shouldReturn('/parent/actions')
        ;
        $this
            ->build(new Route(null), 'base_name', 'App:Test', 'update', null, $parent)
            ->getPath()
            ->shouldReturn('/parent/tests/{TestId}')
        ;
    }

    function its_build_can_not_guess_bad_resource()
    {
        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringBuild(new Route(null), 'base_name', 'BadResource', 'action')
        ;
    }
}
