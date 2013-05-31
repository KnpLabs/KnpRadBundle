<?php

namespace spec\Knp\RadBundle\AppBundle;

use PhpSpec\ObjectBehavior;

class ConfigurationFactorySpec extends ObjectBehavior
{
    /**
     * @param Knp\RadBundle\AppBundle\ConfigurableBundleInterface    $bundle
     * @param Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    function it_should_create_configuration($bundle, $container)
    {
        $config = $this->createConfiguration($bundle, array('some', 'options'), $container);
        $config->shouldBeAnInstanceOf('Knp\RadBundle\AppBundle\Configuration');
        $config->getBundle()->shouldReturn($bundle);
    }
}
