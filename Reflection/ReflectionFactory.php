<?php

namespace Knp\RadBundle\Reflection;

class ReflectionFactory
{
    public function createReflectionClass($class)
    {
        return new \ReflectionClass($class);
    }
}
