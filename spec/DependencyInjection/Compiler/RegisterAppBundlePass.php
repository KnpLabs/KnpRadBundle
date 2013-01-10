<?php

namespace spec\Knp\RadBundle\DependencyInjection\Compiler;

use PHPSpec2\ObjectBehavior;
use Knp\RadBundle\AppBundle\Bundle;

class RegisterAppBundlePass extends ObjectBehavior
{
    /**
     * param Knp\RadBundle\AppBundle\Bundle $bundle
     * @param Symfony\Component\HttpKernel\Bundle\BundleInterface $bundle
     * @param Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param Symfony\Component\HttpKernel\Kernel $kernel
     */
    function let($bundle)
    {
        $this->beConstructedWith($bundle);
    }

    /**
     * @param Symfony\Component\HttpKernel\Bundle\BundleInterface  $secondRadbundle
     **/
    function it_should_refuse_to_use_two_rad_bundles($secondRadBundle, $bundle, $container)
    {
        $container->getParameter('kernel.bundles')->willReturn(array('Knp\RadBundle\AppBundle\Bundle', 'Knp\RadBundle\AppBundle\Bundle'));

        $this->shouldThrow(new \LogicException('Only one rad bundle is authorized'))->duringProcess($container);
    }

    /**
     * @param Symfony\Component\DependencyInjection\Definition       $viewListenerDef
     **/
    function it_should_add_app_bundle_to_view_listener($viewListenerDef, $bundle, $container)
    {
        $container->getParameter('kernel.bundles')->willReturn(array('Knp\RadBundle\AppBundle\Bundle'));
        $bundle->getName()->willReturn('TestBundle');
        $container->hasDefinition('knp_rad.view.listener')->willReturn(true);
        $container->hasDefinition('knp_rad.form.type_creator')->willReturn(false);
        $container->getDefinition('knp_rad.view.listener')->willReturn($viewListenerDef);

        $viewListenerDef->addMethodCall('setAppBundleName', array('TestBundle'))->shouldBeCalled();

        $this->process($container);
    }

    /**
     * @param Symfony\Component\DependencyInjection\Definition       $formTypeCreatorDef
     **/
    function it_should_add_app_bundle_to_form_type_creator($formTypeCreatorDef, $bundle, $container)
    {
        $container->getParameter('kernel.bundles')->willReturn(array('Knp\RadBundle\AppBundle\Bundle'));
        $bundle->getNamespace()->shouldBeCalled()->willReturn('TestBundle');
        $container->hasDefinition('knp_rad.view.listener')->willReturn(false);
        $container->hasDefinition('knp_rad.form.type_creator')->willReturn(true);
        $container->getDefinition('knp_rad.form.type_creator')->willReturn($formTypeCreatorDef);

        $formTypeCreatorDef->addMethodCall('setAppBundleNamespace', array('TestBundle'))->shouldBeCalled();

        $this->process($container);
    }
}
