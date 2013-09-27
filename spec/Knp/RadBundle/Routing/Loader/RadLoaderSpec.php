<?php

namespace spec\Knp\RadBundle\Routing\Loader;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RadLoaderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Knp\RadBundle\Routing\Loader\RadLoader');
    }

    function it_should_be_a_valid_loader()
    {
        $this->shouldHaveType('Symfony\Component\Config\Loader\LoaderInterface');
    }

    function its_supports_should_only_support_rad_definition()
    {
        $this->supports(array(), 'InvalidDefinition')->shouldReturn(false);
        $this->supports(array(), '@valid_definition')->shouldReturn(true);
    }

    function its_load_should_guess_the_routes_name()
    {
        $this
            ->load(array(
                'actions' => array(
                    'some_action' => array(
                        'methods' => 'GET',
                        'pattern' => '/some'
                    )
                ),
                'resource' => 'Bundle:Resource'
            ), '@SomeName')
            ->get('some_name_some_action')
            ->shouldHaveType('Symfony\Component\Routing\Route')
        ;

        $this
            ->load(array(
                'actions' => array(
                    'some_action' => array(
                        'methods' => 'GET',
                        'pattern' => '/action'
                    )
                )
            ), '@Bundle:Namespace\\SubNamespace/Resource')
            ->get('bundle_namespace_sub_namespace_resource_some_action')
            ->shouldHaveType('Symfony\Component\Routing\Route')
        ;
    }

    function its_load_should_guess_ressource_with_name_when_resource_is_not_precised()
    {
        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringLoad(array(
                'actions' => array(
                    'foo' => array(
                        'methods' => 'GET',
                        'pattern' => '/foo'
                    )
                )
            ), '@InvalidResource')
        ;

        $this
            ->shouldNotThrow('InvalidArgumentException')
            ->duringLoad(array(
                'actions' => array(
                    'foo' => array(
                        'methods' => 'GET',
                        'pattern' => '/foo'
                    )
                )
            ), '@Bundle:Resource')
        ;
    }

    function its_load_should_defined_seven_rest_actions_when_no_actions_is_precised()
    {
        $this
            ->load(array(), '@Foo:Bar')
            ->count()
            ->shouldReturn(7)
        ;

        $this
            ->load(array(), '@Foo:Bar')
            ->get('foo_bar_index')
            ->shouldHaveType('Symfony\Component\Routing\Route')
        ;
        $this
            ->load(array(), '@Foo:Bar')
            ->get('foo_bar_new')
            ->shouldHaveType('Symfony\Component\Routing\Route')
        ;
        $this
            ->load(array(), '@Foo:Bar')
            ->get('foo_bar_create')
            ->shouldHaveType('Symfony\Component\Routing\Route')
        ;
        $this
            ->load(array(), '@Foo:Bar')
            ->get('foo_bar_show')
            ->shouldHaveType('Symfony\Component\Routing\Route')
        ;
        $this
            ->load(array(), '@Foo:Bar')
            ->get('foo_bar_edit')
            ->shouldHaveType('Symfony\Component\Routing\Route')
        ;
        $this
            ->load(array(), '@Foo:Bar')
            ->get('foo_bar_update')
            ->shouldHaveType('Symfony\Component\Routing\Route')
        ;
        $this
            ->load(array(), '@Foo:Bar')
            ->get('foo_bar_delete')
            ->shouldHaveType('Symfony\Component\Routing\Route')
        ;
    }

    function its_load_can_autocomplete_default_action_definition()
    {
        $this
            ->load(array(
                'actions' => array(
                    'new' => null
                )
            ), '@My:Resource')
            ->get('my_resource_new')
            ->shouldHaveType('Symfony\Component\Routing\Route')
        ;
    }

    function its_load_can_t_autocomplete_non_default_actions()
    {
        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringLoad(array(
                'actions' => array(
                    'no_default' => null
                )
            ), '@Test:Resource')
        ;
    }

    /**
     * @param Knp\RadBundle\Routing\Builder\RoutePartBuilderInterface $builder1
     * @param Knp\RadBundle\Routing\Builder\RoutePartBuilderInterface $builder2
     */
    function its_load_should_use_builder_for_building_each_actions_route($builder1, $builder2)
    {
        $this->addBuilder($builder1);
        $this->addBuilder($builder2);

        $builder1->build(Argument::cetera())->shouldBeCalled(1);
        $builder2->build(Argument::cetera())->shouldBeCalled(1);

        $this->load(array(), '@Foo:Bar');
    }
}
