<?php

namespace spec\Knp\RadBundle\Routing\Builder;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Routing\Route;

class MethodPartBuilderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Knp\RadBundle\Routing\Builder\MethodPartBuilder');
    }

    function it_should_be_a_alid_part_builder()
    {
        $this->shouldHaveType('Knp\RadBundle\Routing\Builder\RoutePartBuilderInterface');
    }

    function its_build_should_set_up_the_a_route_method()
    {
        $this
            ->build(new Route(null), 'base_name', 'App:Test', 'action', array(
                'methods' => array('GET', 'POST'),
            ))
            ->getMethods()
            ->shouldReturn(array('GET', 'POST'))
        ;
        $this
            ->build(new Route(null), 'base_name', 'App:Test', 'action', array(
                'methods' => 'GET',
            ))
            ->getMethods()
            ->shouldReturn(array('GET'))
        ;
    }

    function its_build_should_determine_default_methods_of_default_actions()
    {
        $this
            ->build(new Route(null), 'base_name', 'App:Test', 'index')
            ->getMethods()
            ->shouldReturn(array('GET'))
        ;
        $this
            ->build(new Route(null), 'base_name', 'App:Test', 'new')
            ->getMethods()
            ->shouldReturn(array('GET'))
        ;
        $this
            ->build(new Route(null), 'base_name', 'App:Test', 'create')
            ->getMethods()
            ->shouldReturn(array('POST'))
        ;
        $this
            ->build(new Route(null), 'base_name', 'App:Test', 'show')
            ->getMethods()
            ->shouldReturn(array('GET'))
        ;
        $this
            ->build(new Route(null), 'base_name', 'App:Test', 'edit')
            ->getMethods()
            ->shouldReturn(array('GET'))
        ;
        $this
            ->build(new Route(null), 'base_name', 'App:Test', 'update')
            ->getMethods()
            ->shouldReturn(array('PUT', 'PATCH'))
        ;
        $this
            ->build(new Route(null), 'base_name', 'App:Test', 'delete')
            ->getMethods()
            ->shouldReturn(array('DELETE'))
        ;
    }

    function its_build_should_take_parent_methods_if_no_methods_are_given()
    {
        $parent = new Route(null);
        $parent->setMethods('PUT');
        $this
            ->build(new Route(null), 'base_name', 'App:Test', 'action', array(), array('action' => $parent))
            ->getMethods()
            ->shouldReturn(array('PUT'))
        ;
        $this
            ->build(new Route(null), 'base_name', 'App:Test', 'action', array(
                'methods' => 'GET'
            ), array('action' => $parent))
            ->getMethods()
            ->shouldReturn(array('GET'))
        ;
    }
}
