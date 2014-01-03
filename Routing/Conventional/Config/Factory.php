<?php

namespace Knp\RadBundle\Routing\Conventional\Config;

use Knp\RadBundle\Routing\Conventional\Config;

class Factory
{
    private $parser;
    private $index = array();

    public function __construct(Parser $parser = null)
    {
        $this->parser = $parser ?: new Parser;
    }

    public function all($path)
    {
        $rawConfigs = $this->parser->parse($path);
        if (empty($rawConfigs)) {
            return array();
        }

        $all = array();
        foreach ($rawConfigs as $name => $rawConfig) {
            $all[] = $this->get($name, $rawConfigs);
        }

        return $all;
    }

    private function get($name, array $rawConfigs)
    {
        if (isset($this->index[$name])) {
            return $this->index[$name];
        }
        if (!array_key_exists($name, $rawConfigs)) {
            throw new \InvalidArgumentException(
                sprintf('no "%s" in [%s]', $name, implode(', ', array_keys($rawConfigs)))
            );
        }
        $rawConfig = $rawConfigs[$name];
        $parent = null;
        if (isset($rawConfig['parent'])) {
            $parentCollection = $this->get($rawConfig['parent'], $rawConfigs);
            $parent = $parentCollection->getRepresentant();
        }

        return $this->index[$name] = new Config($name, $rawConfig, $parent);
    }
}
