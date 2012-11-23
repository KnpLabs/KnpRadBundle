<?php

namespace spec\Knp\RadBundle\DependencyInjection\Compiler;

use PHPSpec2\ObjectBehavior;

class RegisterAppBundlePass extends ObjectBehavior
{
    /**
     * @param Symfony\Component\HttpKernel\Bundle\BundleInterface $bundle
     * @param Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    function let($bundle)
    {
        $this->beConstructedWith($bundle);
    }

    /**
     * @param Symfony\Component\DependencyInjection\Definition       $viewListenerDef
     **/
    function it_should_add_app_bundle_to_view_listener($viewListenerDef, $bundle, $container)
    {
        $bundle->getName()->shouldBeCalled()->willReturn('TestBundle');
        $container->hasDefinition('knp_rad.view.listener')->willReturn(true);
        $container->getDefinition('knp_rad.view.listener')->willReturn($viewListenerDef);

        $viewListenerDef->addMethodCall('setAppBundleName', array('TestBundle'))->shouldBeCalled();

        $this->process($container);
    }
}
