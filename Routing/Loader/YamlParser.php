<?php

namespace Knp\RadBundle\Routing\Loader;

use Symfony\Component\Yaml\Yaml;

class YamlParser
{
    public function parse($file)
    {
        return Yaml::parse($file);
    }

    public function dump($value, $file)
    {
        return file_put_contents($file, Yaml::dump($value));
    }
}
