<?php

namespace spec\Knp\RadBundle\DependencyInjection\Compiler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RemoveUnavailableServicesPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Knp\RadBundle\DependencyInjection\Compiler\RemoveUnavailableServicesPass');
    }

    function it_should_remove_services_when_dependant_service_does_not_exist(ContainerBuilder $container)
    {
        $container->findTaggedServiceIds('remove-when-missing')->willReturn(array(
            'knp_rad.form.manager' => array(
                array('service' => 'form.factory'),
            ),
        ));

        $container->hasDefinition('form.factory')->willReturn(false);
        $container->hasAlias('form.factory')->willReturn(false);

        $container->removeDefinition('knp_rad.form.manager')->shouldBeCalled();

        $this->process($container);
    }

    function it_should_remove_services_when_one_of_dependant_service_does_not_exist(ContainerBuilder $container)
    {
        $container->findTaggedServiceIds('remove-when-missing')->willReturn(array(
            'knp_rad.form.type_creator' => array(
                array('service' => 'form.factory'),
                array('service' => 'form.registry'),
            ),
        ));

        $container->hasDefinition('form.factory')->willReturn(true);
        $container->hasAlias('form.factory')->willReturn(true);

        $container->hasDefinition('form.registry')->willReturn(false);
        $container->hasAlias('form.registry')->willReturn(false);

        $container->removeDefinition('knp_rad.form.type_creator')->shouldBeCalled();

        $this->process($container);
    }
}
