<?php

namespace Knp\RadBundle\Twig;

use Twig_NodeInterface;
use Twig_Node_Expression;

class FlashesNodeFactory
{
    /**
     * @todo reintroduct typehints
     */
    public function createFlashesNode(/* Twig_Node_Expression */ $types = null, /* Twig_Node_Expression */ $catalog = null, Twig_NodeInterface $body, $line)
    {
        return new FlashesNode($types, $catalog, $body, $line);
    }
}
