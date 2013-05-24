<?php

namespace spec\Knp\RadBundle\AppBundle;

use PHPSpec2\ObjectBehavior;

class ContainerExtension extends ObjectBehavior
{
    /**
     * @param Knp\RadBundle\AppBundle\ConfigurableBundleInterface    $bundle
     * @param Knp\RadBundle\AppBundle\ConfigurationFactory           $configFactory
     * @param Knp\RadBundle\AppBundle\Configuration                  $config
     * @param Symfony\Component\Config\Definition\Processor          $configProcessor
     * @param Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    function let($bundle, $configFactory, $configProcessor, $container)
    {
        $this->beConstructedWith($bundle, $configFactory, $configProcessor);
    
        $bundle->getPath()->willReturn('/path/to/the/src/App');

        $container->getParameter('kernel.environment')->willReturn('some_env');
    }

    function its_getConfiguration_should_return_rad_configuration($container, $bundle, $configFactory, $config)
    {
        $configFactory->createConfiguration($bundle, array('some', 'options'), $container)->willReturn($config);

        $this->getConfiguration(array('some', 'options'), $container)->shouldReturn($config);
    }

    function its_load_should_use_bundle_to_build_container($container, $bundle, $configFactory, $config, $configProcessor)
    {
        $configFactory->createConfiguration(ANY_ARGUMENTS)->willReturn($config);
        $configProcessor->processConfiguration(ANY_ARGUMENTS)->willReturn(array('processed', 'options'));

        $bundle->buildContainer(array('processed', 'options'), $container)->shouldBeCalled();

        $this->load(array('not', 'processed', 'options'), $container);
    }
}