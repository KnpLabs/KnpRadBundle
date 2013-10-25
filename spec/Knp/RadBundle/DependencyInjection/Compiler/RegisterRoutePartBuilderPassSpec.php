<?php

namespace spec\Knp\RadBundle\DependencyInjection\Compiler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RegisterRoutePartBuilderPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Knp\RadBundle\DependencyInjection\Compiler\RegisterRoutePartBuilderPass');
    }

    function it_should_be_a_valid_compiler_pass()
    {
        $this->shouldHaveType('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface');
    }

    /**
     * @param Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param Symfony\Component\DependencyInjection\Definition       $definition
     */
    function its_process_should_add_method_call_to_route_resolver_with_tagged_route_part_builder(
        $container,
        $definition
    )
    {
        $container->hasDefinition('knp_rad.routing.rad_loader')->willReturn(true);
        $container->getDefinition('knp_rad.routing.rad_loader')->willReturn($definition);
        $container->findTaggedServiceIds('knp_rad.routing.part_builder')->willReturn(array(
            'id' => array('some_attributes')
        ));

        $definition->addMethodCall(Argument::cetera())->shouldBeCalled(1);
        $this->process($container);
    }

    /**
     * @param Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param Symfony\Component\DependencyInjection\Definition       $definition
     */
    function its_process_should_not_do_nothing_when_no_routing_resolver_is_defined(
        $container,
        $definition
    )
    {
        $container->hasDefinition('knp_rad.routing.rad_loader')->willReturn(false);
        $definition->addMethodCall(Argument::cetera())->shouldNotBeCalled();

        $this->process($container);
    }
}
