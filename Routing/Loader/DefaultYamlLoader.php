<?php

namespace Knp\RadBundle\Routing\Loader;

use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

class DefaultYamlLoader extends YamlFileLoader
{
    public function load($resource, $type = null)
    {
        $filename = tempnam(sys_get_temp_dir(), 'routing');
        file_put_contents(
            Yaml::dump(array($type => $resource)),
            $filename
        );

        return parent::load($filename, $type);
    }
}
