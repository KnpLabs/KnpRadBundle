<?php

namespace spec\Knp\RadBundle\DependencyInjection\Definition;

use PhpSpec\ObjectBehavior;

class FormTypeExtensionDefinitionFactorySpec extends ObjectBehavior
{
    /**
     * @param  Knp\RadBundle\Reflection\ReflectionFactory $reflectionFactory
     * @param  ReflectionClass $reflClass
     * @param  Knp\RadBundle\DependencyInjection\ReferenceFactory $referenceFactory
     * @param  Symfony\Component\DependencyInjection\Reference $containerRef
     */
    function let($reflectionFactory, $reflClass, $referenceFactory, $containerRef)
    {
        $this->beConstructedWith($reflectionFactory, $referenceFactory);

        $reflectionFactory->createReflectionClass('App\Form\Extension\WhateverTypeExtension')->willReturn($reflClass);
        $referenceFactory->createReference('service_container')->willReturn($containerRef);
    }

    function it_should_create_form_type_definition_for_the_given_class_name($reflClass)
    {
        $reflClass->implementsInterface('Symfony\Component\DependencyInjection\ContainerAwareInterface')->willReturn(false);

        $definition = $this->createDefinition('App\Form\Extension\WhateverTypeExtension');

        $definition->shouldBeAnInstanceOf('Symfony\Component\DependencyInjection\Definition');
        $definition->getClass()->shouldBe('App\Form\Extension\WhateverTypeExtension');
        $definition->isPublic()->shouldBe(true);
        $definition->hasTag('form.type_extension')->shouldBe(true);
        $definition->getMethodCalls()->shouldReturn(array());
    }

    function it_should_add_method_call_to_inject_container_for_container_aware_extensions($reflClass, $containerRef)
    {
        $reflClass->implementsInterface('Symfony\Component\DependencyInjection\ContainerAwareInterface')->willReturn(true);

        $definition = $this->createDefinition('App\Form\Extension\WhateverTypeExtension');

        $definition->shouldBeAnInstanceOf('Symfony\Component\DependencyInjection\Definition');
        $definition->getClass()->shouldBe('App\Form\Extension\WhateverTypeExtension');
        $definition->isPublic()->shouldBe(true);
        $definition->hasTag('form.type_extension')->shouldBe(true);
        $definition->getMethodCalls()->shouldReturn(array(array('setContainer', array($containerRef->getWrappedObject()))));
    }
}
