<?php

namespace spec\Knp\RadBundle\DependencyInjection\Definition;

use PhpSpec\ObjectBehavior;

class SecurityVoterFactorySpec extends ObjectBehavior
{
    /**
     * @param  Knp\RadBundle\Reflection\ReflectionFactory $reflectionFactory
     * @param  Knp\RadBundle\DependencyInjection\ReferenceFactory $referenceFactory
     * @param  ReflectionClass $reflClass
     * @param  Symfony\Component\DependencyInjection\Reference $containerRef
     */
    function let($reflectionFactory, $referenceFactory, $reflClass, $containerRef)
    {
        $this->beConstructedWith($reflectionFactory, $referenceFactory);

        $reflectionFactory->createReflectionClass('App\Security\CheeseVoter')->willReturn($reflClass);
        $referenceFactory->createReference('service_container')->willReturn($containerRef);
    }

    function it_should_create_a_service_definition_for_the_specified_security_voter_class($reflClass)
    {
        $reflClass->implementsInterface('Symfony\Component\DependencyInjection\ContainerAwareInterface')->willReturn(false);

        $definition = $this->createDefinition('App\Security\CheeseVoter');
        $definition->shouldBeAnInstanceOf('Symfony\Component\DependencyInjection\Definition');
        $definition->getClass()->shouldReturn('App\Security\CheeseVoter');
        $definition->getMethodCalls()->shouldReturn(array());
    }

    function it_should_add_method_call_to_inject_container_to_container_aware_voters($reflClass, $containerRef)
    {
        $reflClass->implementsInterface('Symfony\Component\DependencyInjection\ContainerAwareInterface')->willReturn(true);

        $definition = $this->createDefinition('App\Security\CheeseVoter');
        $definition->shouldBeAnInstanceOf('Symfony\Component\DependencyInjection\Definition');
        $definition->getClass()->shouldReturn('App\Security\CheeseVoter');
        $definition->getMethodCalls()->shouldReturn(array(array('setContainer', array($containerRef->getWrappedSubject()))));
    }
}
