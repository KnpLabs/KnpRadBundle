<?php

namespace Knp\Bundle\RadBundle\Routing\Generator;

use Symfony\Component\Routing\Generator\UrlGenerator as BaseUrlGenerator;

/**
 * This URL generator class adds the ability to read the parameters from an
 * object
 */
class UrlGenerator extends BaseUrlGenerator
{
    /**
     * {@override}
     */
    protected function doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $absolute)
    {
        if (is_object($parameters)) {
            $parameters = $this->readObjectParameters($parameters, $variables);
        }

        return parent::doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $absolute);
    }

    private function readObjectParameters($object, array $variables)
    {
        $parameters = array();

        foreach ($variables as $variable) {
            $parameters[$variable] = $this->readObjectParameter($variable);
        }

        return $parameters;
    }

    private function readObjectParameter($object, $parameterName)
    {
        $property = $this->camelize($parameterName);
        $getter   = sprintf('get%s', ucfirst($property));

        if (is_callable($getter, $object)) {
            return $object->$getter();
        } elseif (property_exists($property, $object)) {
            return $object->$property;
        }
    }

    private function camelize($string)
    {
        return preg_replace_callback('/(^|_|\.)+(.)/', function ($match) { return ('.' === $match[1] ? '_' : '').strtoupper($match[2]); }, $string);
    }
}
