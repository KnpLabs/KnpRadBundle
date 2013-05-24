<?php

namespace spec\Knp\RadBundle\DependencyInjection\Definition;

use PhpSpec\ObjectBehavior;

class DoctrineRepositoryFactorySpec extends ObjectBehavior
{
    function it_should_create_repository_definition_for_the_given_classe_name()
    {
        $definition = $this->createDefinition('App\Entity\Cheese');
        $definition->shouldBeAnInstanceOf('Symfony\Component\DependencyInjection\Definition');

        $definition->getFactoryService()->shouldReturn('doctrine');
        $definition->getFactoryMethod()->shouldReturn('getRepository');
        $definition->getArguments()->shouldReturn(array('App\Entity\Cheese'));
        $definition->isPublic()->shouldReturn(true);
    }
}
