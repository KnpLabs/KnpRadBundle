<?php

namespace spec\Knp\RadBundle\Filesystem;

use PhpSpec\ObjectBehavior;

class PathExpanderSpec extends ObjectBehavior
{
    /**
     * @param  Symfony\Component\HttpKernel\KernelInterface        $kernel
     * @param  Symfony\Component\HttpKernel\Bundle\BundleInterface $bundle
     */
    public function let($kernel, $bundle)
    {
        $this->beConstructedWith($kernel);

        $kernel->getBundle('App')->willReturn($bundle);

        $bundle->getPath()->willReturn('/my/project/src/App');
    }

    public function it_should_expand_bundle_paths($kernel, $bundle)
    {
        $this->expand('@App/Resources/config/routing.yml')->shouldReturn('/my/project/src/App/Resources/config/routing.yml');
    }

    public function it_should_not_change_other_paths()
    {
        $this->expand('/some/absolute/path')->shouldReturn('/some/absolute/path');
    }
}
