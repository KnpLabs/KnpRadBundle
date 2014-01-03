<?php

namespace Knp\RadBundle\Routing\Conventional\Generator;

use Knp\RadBundle\Routing\Conventional\Config;
use Knp\RadBundle\Routing\Conventional\Generator;

class ControllerName implements Generator
{
    private $map = array(
        'create' => 'new',
        'update' => 'edit',
    );

    public function __construct(array $map = null)
    {
        $this->map = $map ?: $this->map;
    }

    public function generate(Config $config)
    {
        $controller = $this->getControllerName($config);
        $name = isset($this->map[$config->name]) ? $this->map[$config->name] : $config->name;

        return sprintf('%s:%s%s', $controller, $name, $this->getActionSuffix($config));
    }

    private function getActionSuffix(Config $config)
    {
        if ($config->getController()) {
            return 'Action';
        }
    }

    private function getControllerName(Config $config)
    {
        if ($config->getController()) {
            return $config->getController();
        }

        return $this->generateControllerHierarchy($config);
    }

    private function generateControllerHierarchy(Config $config)
    {
        $parts = array();
        if ($config->parent) {
            $parts[] = $this->generateControllerHierarchy($config->parent);
        }
        $parts[] = $this->getPrefix($config);

        return implode('/', array_filter($parts));
    }

    /**
     * prefix is the name (by default). This name can be of form: "App:Something"
     *
     * @return string
     **/
    private function getPrefix(Config $config)
    {
        $parts = explode(':', $config->name);

        if (1 === count($parts)) {
            return;
        }

        if ($config->parent && 2 === count($parts)) {
            array_shift($parts);
            return implode('/', $parts);
        }

        if (!$config->parent) {
            return implode(':', $parts);
        }
    }
}
