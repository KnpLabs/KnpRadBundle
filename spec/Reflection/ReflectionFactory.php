<?php

namespace spec\Knp\RadBundle\Reflection;

use PHPSpec2\ObjectBehavior;

class ReflectionFactory extends ObjectBehavior
{
    function it_should_create_reflection_class_instances()
    {
        $this->createReflectionClass('stdClass')->shouldBeAnInstanceOf('ReflectionClass');
    }
}
