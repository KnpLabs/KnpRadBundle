<?php

namespace spec\Knp\RadBundle\Reflection;

use PhpSpec\ObjectBehavior;

class ReflectionFactorySpec extends ObjectBehavior
{
    function it_should_create_reflection_class_instances()
    {
        $this->createReflectionClass('stdClass')->shouldBeAnInstanceOf('ReflectionClass');
    }
}
