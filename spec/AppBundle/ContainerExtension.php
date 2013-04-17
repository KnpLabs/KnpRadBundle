<?php

namespace spec\Knp\RadBundle\AppBundle;

use PHPSpec2\ObjectBehavior;

class ContainerExtension extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('my_app');
    }

    function it_should_be_aliased_app()
    {
        $this->getAlias()->shouldReturn('app');
    }

    /**
     * @param Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    function it_should_add_configuration_as_container_parameters($container)
    {
        $container->getParameter('kernel.environment')->willReturn('dev');
        $container->setParameter('app.foo', 'bar')->shouldBeCalled();
        $container->setParameter('app.baz', array('boz' => 'for'))->shouldBeCalled();

        $this->load(array(array(
            'foo' => 'bar',
            'baz' => array('boz' => 'for')
        )), $container);
    }
}
