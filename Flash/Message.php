<?php

namespace Knp\RadBundle\Flash;

class Message implements \Serializable
{
    private $template;
    private $parameters;
    private $pluralization;

    /**
     * @param string $template
     * @param array  $parameters
     * @param string $pluralization
     */
    public function __construct($template, array $parameters = array(), $pluralization = null)
    {
        $this->template      = $template;
        $this->parameters    = $parameters;
        $this->pluralization = $pluralization;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return string
     */
    public function getPluralization()
    {
        return $this->pluralization;
    }

    public function __toString()
    {
        return strtr($this->template, $this->parameters);
    }

    public function serialize()
    {
        return serialize(array(
            $this->template,
            $this->parameters,
            $this->pluralization
        ));
    }

    public function unserialize($data)
    {
        list(
            $this->template,
            $this->parameters,
            $this->pluralization
        ) = unserialize($data);
    }
}
