<?php

namespace Knp\RadBundle\Form;

class RecursiveChildrenIterator extends \IteratorIterator implements \RecursiveIterator
{
    /**
     * {@inheritdoc}
     */
    public function getChildren()
    {
        return new static($this->current());
    }

    /**
     *{@inheritdoc}
     */
    public function hasChildren()
    {
        return 0 !== count($this->current()->getIterator());
    }
}
