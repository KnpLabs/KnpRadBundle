<?php

namespace spec\Knp\RadBundle\Twig;

use PHPSpec2\ObjectBehavior;

class FlashesNode extends ObjectBehavior
{
    /**
     * @param  Twig_Node_Expression $types
     * @param  Twig_Node_Expression $catalog
     * @param  Twig_NodeInterface   $body
     */
    function let($types, $catalog, $body)
    {
        $this->beConstructedWith($types, $catalog, $body, 123);
    }

    function it_should_be_a_twig_node()
    {
        $this->shouldBeAnInstanceOf('Twig_Node');
    }

    function it_should_be_constructible_with_no_types($catalog, $body)
    {
        $this->shouldNotThrow()->during('__construct', array(null, $catalog, $body, 123));
    }

    function it_should_be_constructible_with_no_catalog($types, $body)
    {
        $this->shouldNotThrow()->during('__construct', array($types, null, $body, 123));
    }

    /**
     * @param  Twig_Compiler $compiler
     */
    function it_should_compile($compiler, $types, $catalog, $body)
    {
        $compiler->addDebugInfo($this)->shouldBeCalled()->willReturn($compiler);
        $compiler->subcompile($types)->shouldBeCalled()->willReturn($compiler);
        $compiler->subcompile($catalog)->shouldBeCalled()->willReturn($compiler);
        $compiler->subcompile($body)->shouldBeCalled()->willReturn($compiler);

        $compiler->write(ANY_ARGUMENTS)->willReturn($compiler);
        $compiler->raw(ANY_ARGUMENTS)->willReturn($compiler);
        $compiler->indent(ANY_ARGUMENTS)->willReturn($compiler);
        $compiler->outdent(ANY_ARGUMENTS)->willReturn($compiler);

        $this->compile($compiler);
    }
}
