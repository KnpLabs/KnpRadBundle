<?php

namespace spec\Knp\RadBundle\DependencyInjection;

use PHPSpec2\ObjectBehavior;

class DefinitionManipulator extends ObjectBehavior
{
    /**
     * @param  Symfony\Component\DependencyInjection\Definition $definition
     */
    function let($definition)
    {
    }

    function it_should_append_an_element_to_the_array_value_of_the_specified_argument($definition)
    {
        $definition->getArgument(1)->willReturn(array('Camembert', 'Roquefort'));
        $definition->replaceArgument(1, array('Camembert', 'Roquefort', 'Munster'))->shouldBeCalled();

        $this->appendArgumentValue($definition, 1, 'Munster');
    }
}
