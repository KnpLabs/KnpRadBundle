<?php

namespace Knp\RadBundle\Routing\Conventional\Generator;

use Knp\RadBundle\Routing\Conventional\Config;
use Knp\RadBundle\Routing\Conventional\Generator;

class ViewName implements Generator
{
    public function generate(Config $config)
    {
        return sprintf('%s:%s', $this->getViewName($config), $config->name);
    }

    private function getViewName(Config $config)
    {
        if ($config->getView()) {
            return $config->getView();
        }

        return $this->generateViewHierarchy($config);
    }

    private function generateViewHierarchy(Config $config)
    {
        $parts = array();
        if ($config->parent) {
            $parts[] = $this->generateViewHierarchy($config->parent);
        }
        $parts[] = $this->getPrefix($config);

        return implode('/', array_filter($parts));
    }

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
