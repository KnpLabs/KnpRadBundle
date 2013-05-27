<?php

namespace spec\Knp\RadBundle\DependencyInjection;

use PhpSpec\ObjectBehavior;

class ReferenceFactorySpec extends ObjectBehavior
{
    function it_should_create_a_reference_for_the_specified_service_id()
    {
        $reference = $this->createReference('some.service.id');
        $reference->shouldBeAnInstanceOf('Symfony\Component\DependencyInjection\Reference');
    }
}
