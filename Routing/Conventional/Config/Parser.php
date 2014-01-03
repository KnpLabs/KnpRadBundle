<?php

namespace Knp\RadBundle\Routing\Conventional\Config;

use Symfony\Component\Yaml\Yaml;

class Parser
{
    public function parse($path)
    {
        return Yaml::parse($path);
    }
}
