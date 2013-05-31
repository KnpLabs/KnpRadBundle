<?php

namespace spec\Knp\RadBundle\AppBundle;

use PhpSpec\ObjectBehavior;

class TreeBuilderFactorySpec extends ObjectBehavior
{
    function it_should_create_treeBuilder()
    {
        $tb = $this->createTreeBuilder();
        $tb->shouldBeAnInstanceOf('Symfony\Component\Config\Definition\Builder\TreeBuilder');
    }
}
