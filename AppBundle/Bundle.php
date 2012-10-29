<?php

namespace Knp\RadBundle\AppBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle as BaseBundle;

class Bundle extends BaseBundle
{
    private $path;

    public function __construct($path, $name = 'App')
    {
        $this->path = $path;
        $this->name = $name;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getNamespace()
    {
        return $this->name;
    }

    public function getContainerExtension()
    {
        if ($extension = parent::getContainerExtension()) {
            return $extension;
        }

        return $this->extension = new ContainerExtension($this->path);
    }
}
