<?php

namespace Knp\RadBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Definition;

class DefinitionManipulator
{
    public function appendArgumentValue(Definition $definition, $index, $value)
    {
        $values = $definition->getArgument($index);
        $values[] = $value;

        $definition->replaceArgument($index, $values);
    }
}
