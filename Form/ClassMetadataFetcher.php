<?php

namespace Knp\RadBundle\Form;

class ClassMetadataFetcher
{
    public function getClass($object)
    {
        return $this->reflect($object)->getName();
    }

    public function classExists($classname)
    {
        return class_exists($classname);
    }

    public function newInstance($class, array $args = array())
    {
        return $this->reflect($class)->newInstanceArgs($args);
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

    public function getMethods($object)
    {
        return array_map(function ($method) {
            return $method->getName();
        }, $this->reflect($object)->getMethods());
    }

    public function hasMethod($object, $methodName)
    {
        return $this->reflect($object)->hasMethod($methodName);
    }
}
