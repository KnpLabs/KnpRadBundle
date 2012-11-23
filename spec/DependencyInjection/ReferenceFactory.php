<?php

namespace spec\Knp\RadBundle\DependencyInjection;

use PHPSpec2\ObjectBehavior;

class ReferenceFactory extends ObjectBehavior
{
    function it_should_create_a_reference_for_the_specified_service_id()
    {
        $reference = $this->createReference('some.service.id');
        $reference->shouldBeAnInstanceOf('Symfony\Component\DependencyInjection\Reference');
    }
}
