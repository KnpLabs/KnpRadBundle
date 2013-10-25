<?php

namespace Knp\RadBundle\Resource;

class RadResourceAction
{
    private $name;

    private $pattern;

    private $methods;

    private $defaults;

    private $requirements;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function setPattern($pattern)
    {
        $this->pattern = $pattern;

        return $this;
    }

    public function getPattern()
    {
        return $this->pattern;
    }

    public function setMethods($methods)
    {
        $this->methods = (array)$methods;

        return $this;
    }

    public function getMethods()
    {
        return $this->methods;
    }

    public function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;

        return $this;
    }

    public function getDefaults()
    {
        return $this->defaults;
    }

    public function setRequirements(array $requirements)
    {
        $this->requirements = $requirements;

        return $this;
    }

    public function getRequirements()
    {
        return $this->requirements;
    }
}
