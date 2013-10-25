<?php

namespace spec\Knp\RadBundle\Resource\Factory;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RadResourceFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Knp\RadBundle\Resource\Factory\RadResourceFactory');
    }

    function its_create_should_return_a_RadResource()
    {
        $this->create('some_resource_name')->shouldHaveType('Knp\RadBundle\Resource\RadResource');
    }
}
