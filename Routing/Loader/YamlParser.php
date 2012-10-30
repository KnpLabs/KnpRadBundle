<?php

namespace Knp\RadBundle\Routing\Loader;

use Symfony\Component\Yaml\Yaml;

class YamlParser
{
    public function parse($file)
    {
        return Yaml::parse($file);
    }
}
