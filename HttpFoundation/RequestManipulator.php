<?php

namespace Knp\RadBundle\HttpFoundation;

use Symfony\Component\HttpFoundation\Request;

class RequestManipulator
{
    public function setAttribute(Request $request, $name, $value)
    {
        $request->attributes->set($name, $value);
    }

    public function getAttribute(Request $request, $name)
    {
        return $request->attributes->get($name);
    }

    public function hasAttribute(Request $request, $name)
    {
        return $request->attributes->has($name);
    }
}
