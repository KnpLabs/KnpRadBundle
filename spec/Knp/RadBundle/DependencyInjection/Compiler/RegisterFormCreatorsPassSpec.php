<?php

namespace spec\Knp\RadBundle\DependencyInjection\Compiler;

use PhpSpec\ObjectBehavior;

class RegisterFormCreatorsPassSpec extends ObjectBehavior
{
    /**
     * @param Knp\RadBundle\DependencyInjection\ReferenceFactory $referenceFactory
     */
    function let($referenceFactory)
    {
        $this->beConstructedWith($referenceFactory);
    }

    function it_should_be_a_compiler_pass()
    {
        $this->shouldBeAnInstanceOf('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface');
    }

    /**
     * @param Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param Symfony\Component\DependencyInjection\Definition       $managerDef
     * @param Symfony\Component\DependencyInjection\Reference        $memoryRef
     * @param Symfony\Component\DependencyInjection\Reference        $typeRef
     */
    function it_should_find_all_creators_and_register_them_in_the_form_manager(
        $referenceFactory, $container, $managerDef, $memoryRef, $typeRef
    )
    {
        $container->hasDefinition('knp_rad.form.manager')->willReturn(true);
        $container->getDefinition('knp_rad.form.manager')->willReturn($managerDef);
        $container->findTaggedServiceIds('knp_rad.form.creator')->willReturn(array(
            'knp_rad.creator.memory' => array(0 => array('priority' => 1)),
            'knp_rad.creator.type'   => array(0 => array('priority' => 2)),
        ));

        $referenceFactory->createReference('knp_rad.creator.memory')->willReturn($memoryRef);
        $referenceFactory->createReference('knp_rad.creator.type')->willReturn($typeRef);

        $managerDef->addMethodCall('registerCreator', array($memoryRef, 1))->shouldBeCalled();
        $managerDef->addMethodCall('registerCreator', array($typeRef, 2))->shouldBeCalled();

        $this->process($container);
    }
}
