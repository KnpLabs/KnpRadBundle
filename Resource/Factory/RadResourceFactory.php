<?php

namespace Knp\RadBundle\Resource\Factory;

use Knp\RadBundle\Resource\RadResource;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\Common\Inflector\Inflector;
use Knp\RadBundle\Resource\Registry;

class RadResourceFactory
{
    public function create($name)
    {
        return new RadResource($name);
    }

    public function createFromDefinition(
        $name,
        array $definition,
        Registry $registry
    )
    {
        if (isset($definition['resource'])) {
            $resource = new RadResource($name);
            $resource->setResource($definition['resource']);
        } else {
            $resource = new RadResource($this->formatNameToResource(
                $name
            ));
            $resource->setResource($name);
        }

        
    }

    private function formatNameToResource($resource)
    {
        return array_map(
            function ($v) {
                return Inflector::tableize(str_replace(array('\\', '/'), '', $v));
            },
            explode(':', $resource)
        );
    }
}
