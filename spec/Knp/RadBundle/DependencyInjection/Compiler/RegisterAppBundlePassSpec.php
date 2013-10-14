<?php

namespace spec\Knp\RadBundle\DependencyInjection\Compiler;

use PhpSpec\ObjectBehavior;
use Knp\RadBundle\AppBundle\Bundle;

class RegisterAppBundlePassSpec extends ObjectBehavior
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
     * @param Symfony\Component\DependencyInjection\Definition $def
     **/
    function it_should_add_app_bundles_to_bundle_guesser($def, $bundle, $container)
    {
        $container->getParameter('kernel.bundles')->willReturn(array('Knp\RadBundle\AppBundle\Bundle'));
        $bundle->getName()->willReturn('TestBundle');
        $container->hasDefinition('knp_rad.bundle.guesser')->willReturn(true);
        $container->getDefinition('knp_rad.bundle.guesser')->willReturn($def);

        $def->getArgument(2)->willReturn(array());
        $def->replaceArgument(2, array('TestBundle'))->shouldBeCalled();

        $this->process($container);
    }
}
