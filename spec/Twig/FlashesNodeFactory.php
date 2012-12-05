<?php

namespace spec\Knp\RadBundle\Twig;

use PHPSpec2\ObjectBehavior;

class FlashesNodeFactory extends ObjectBehavior
{
    /**
     * @param  Twig_Node_Expression $types
     * @param  Twig_Node_Expression $catalog
     * @param  Twig_NodeInterface   $body
     */
    function let()
    {
    }

    function it_should_create_flashes_node_instances($types, $catalog, $body)
    {
        $this->createFlashesNode($types, $catalog, $body, 123)->shouldBeAnInstanceOf('Knp\RadBundle\Twig\FlashesNode');
    }

    function it_should_allow_to_create_flashes_node_with_no_types($catalog, $body)
    {
        $this->createFlashesNode(null, $catalog, $body, 123)->shouldBeAnInstanceOf('Knp\RadBundle\Twig\FlashesNode');
    }

    function it_should_allow_to_create_flashes_node_with_no_catalog($types, $body)
    {
        $this->createFlashesNode($types, null, $body, 123)->shouldBeAnInstanceOf('Knp\RadBundle\Twig\FlashesNode');
    }
}
