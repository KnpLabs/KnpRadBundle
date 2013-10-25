<?php

namespace Knp\RadBundle\Resource;

use Doctrine\Common\Collections\ArrayCollection;

class Registry
{
    private $resources;

    public function __construct()
    {
        $this->resources = array();
    }

    public function addResource(RadResource $resource)
    {
        if ($this->hasResource($resource->getName())) {
            throw new \RuntimeException(sprintf(
                'A resource named %s always exists in the resource registry.',
                $resource->getName()
            ));
        }

        $this->resources[] = $resource;

        return $this;
    }

    public function hasResource($resourceName)
    {
        foreach ($this->resources as $resource) {
            if ($resourceName === $resource->getName()) {
                return true;
            }
        }

        return false;
    }

    public function getResource($resourceName)
    {
        foreach ($this->resources as $resource) {
            if ($resourceName === $resource->getName()) {
                return $resource;
            }
        }

        return null;
    }

    public function getResources()
    {
        return $this->resources;
    }
}
