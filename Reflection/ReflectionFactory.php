<?php

namespace Knp\RadBundle\Reflection;

class ReflectionFactory
{
    public function createReflectionClass($class)
    {
        return new \ReflectionClass($class);
    }

    public function getParameters($controller)
    {
        if (is_array($controller)) {
            $r = new \ReflectionMethod($controller[0], $controller[1]);
        } else {
            $r = new \ReflectionFunction($controller);
        }

        return $r->getParameters();
    }
}
