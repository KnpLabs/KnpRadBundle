<?php

namespace Knp\RadBundle\Resource\Factory;

use Knp\RadBundle\Resource\RadResource;

class RadResourceFactory
{
    public function create($name)
    {
        return new RadResource($name);
    }
}
