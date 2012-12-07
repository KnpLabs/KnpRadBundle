<?php

namespace Knp\RadBundle\Reflection;

class ClassMetadataFetcher
{
    public function getClass($object)
    {
        return $this->reflect($object)->getName();
    }

    public function getShortClassName($object)
    {
        return $this->reflect($object)->getShortName();
    }

    public function classExists($classname)
    {
        return class_exists($classname);
    }

    public function newInstance($class)
    {
        return $this->reflect($class)->newInstance();
    }

    public function reflect($classname)
    {
        return new \ReflectionObject($classname);
    }

    public function getParentClass($object)
    {
        $parentClass = $this->reflect($object)->getParentClass();

        if ($parentClass instanceof \ReflectionClass) {
            return $parentClass->getName();
        }
    }

    public function getMethods($object)
    {
        return array_map(function ($method) {
            return $method->getName();
        }, $this->reflect($object)->getMethods(\ReflectionMethod::IS_PUBLIC));
    }

    public function getProperties($object)
    {
        return array_map(function($property) {
            return $property->getName();
        }, $this->reflect($object)->getProperties(\ReflectionProperty::IS_PUBLIC));
    }

    public function hasMethod($object, $methodName)
    {
        return $this->reflect($object)->hasMethod($methodName);
    }
}
