<?php

namespace Knp\RadBundle\Resource;

use Doctrine\Common\Collections\ArrayCollection;

class RadResource
{
    private $name;

    private $property;

    private $requirement;

    private $parent;

    private $actions;

    public function __construct($name)
    {
        $this->name    = $name;
        $this->actions = new ArrayCollection();
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setProperty($property)
    {
        $this->property = $property;

        return $this;
    }

    public function getProperty()
    {
        return $this->property;
    }

    public function setRequirement($requirement)
    {
        $this->requirement = $requirement;

        return $this;
    }

    public function getRequirement()
    {
        return $this->requirement;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function addAction(RadResourceAction $action)
    {
        $this->actions->add($action);

        return $this;
    }

    public function getAction($action)
    {
        foreach ($this->actions as $value) {
            if ($value->getName() === $action) {

                return $value;
            }
        }

        return null;
    }

    public function hasAction($action)
    {
        foreach ($this->actions as $value) {
            if ($value->getName() === $action) {

                return true;
            }
        }

        return false;
    }

    public function getActions()
    {
        return $this->actions;
    }
}
