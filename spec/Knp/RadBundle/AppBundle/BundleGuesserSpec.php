<?php

namespace spec\Knp\RadBundle\AppBundle;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Knp\RadBundle\Reflection\ReflectionFactory;

class BundleGuesserSpec extends ObjectBehavior
{
    function let(KernelInterface $kernel, ReflectionFactory $reflectionFactory)
    {
        $this->beConstructedWith($kernel, $reflectionFactory, array('App', 'TestBundle'));
    }

    function its_getBundle_gets_a_bundle_instance_given_a_className(BundleInterface $bundle, $kernel, $reflectionFactory, \ReflectionClass $refl)
    {
        $refl->getParentClass()->willReturn(null);
        $refl->getNamespaceName()->willReturn('Vendor\Bundle\TestBundle\Controller');
        $bundle->getName()->willReturn('TestBundle');
        $bundle->getNamespace()->willReturn('Vendor\Bundle\TestBundle');
        $kernel->getBundles()->willReturn(array($bundle));
        $reflectionFactory->createReflectionClass(Argument::any())->willReturn($refl);
        $this->getBundleForClass('Vendor\Bundle\TestBundle\Controller\TestController')->shouldReturn($bundle);
    }

    function its_getBundle_gets_the_default_app_bundle(BundleInterface $bundle, $kernel, $reflectionFactory, \ReflectionClass $refl)
    {
        $refl->getParentClass()->willReturn(null);
        $refl->getNamespaceName()->willReturn('App\Controller');
        $bundle->getName()->willReturn('App');
        $bundle->getNamespace()->willReturn('App');
        $kernel->getBundles()->willReturn(array($bundle));
        $reflectionFactory->createReflectionClass(Argument::any())->willReturn($refl);
        $this->getBundleForClass('App\Controller\TestController')->shouldReturn($bundle);
    }
}
