<?php

namespace Knp\RadBundle\Flash;

class Message
{
    private $template;
    private $parameters;
    private $pluralization;

    public function __construct($template, array $parameters = array(), $pluralization = null)
    {
        $this->template      = $template;
        $this->parameters    = $parameters;
        $this->pluralization = $pluralization;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function getPluralization()
    {
        return $this->pluralization;
    }

    public function __toString()
    {
        return strtr($this->template, $this->parameters);
    }
}
