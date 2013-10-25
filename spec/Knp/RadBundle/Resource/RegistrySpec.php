<?php

namespace spec\Knp\RadBundle\Resource;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Knp\RadBundle\Resource\RadResource;

class RegistrySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Knp\RadBundle\Resource\Registry');
    }

    function its_addResource_should_throw_an_exception_if_the_resource_is_always_register()
    {
        $resource = new RadResource('some_name');
        $again    = new RadResource('some_name');

        $this
            ->addResource($resource)
            ->shouldThrow('RuntimeException')
            ->duringAddResource($again)
        ;
    }

    function its_getResource_should_return_exsisting_resource_or_null()
    {
        $resource = new RadResource('some_name');

        $this->addResource($resource);

        $this->getResource('some_name')->shouldReturn($resource);
        $this->getResource('foo')->shouldReturn(null);
    }

    function its_hasResource_should_return_true_or_false_if_resource_exist_or_not()
    {
        $resource = new RadResource('resource');

        $this->addResource($resource);
        $this->hasResource('resource')->shouldReturn(true);
        $this->hasResource('foo')->shouldReturn(false);
    }
}
