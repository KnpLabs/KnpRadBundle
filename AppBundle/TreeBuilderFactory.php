<?php

namespace Knp\RadBundle\AppBundle;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class TreeBuilderFactory
{
    public function createTreeBuilder()
    {
        return new TreeBuilder;
    }
}