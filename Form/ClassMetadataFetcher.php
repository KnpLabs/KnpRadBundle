<?php

namespace Knp\RadBundle\Form;

class ClassMetadataFetcher
{
    public function classExists($classname)
    {
        return class_exists($classname);
    }

    public function reflect($classname)
    {
        return new \ReflectionClass($classname);
    }

    public function getParentClass($object)
    {
        $parentClass = $this->reflect($classname)->getParentClass();

        if ($parentClass instanceof \ReflectionClass) {
            return $parent->getName();
        }
    }
}
