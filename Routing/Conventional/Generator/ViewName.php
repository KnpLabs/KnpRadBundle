<?php

namespace Knp\RadBundle\Routing\Conventional\Generator;

use Knp\RadBundle\Routing\Conventional\Config;
use Knp\RadBundle\Routing\Conventional\Generator;

class ViewName implements Generator
{
    public function generate(Config $config)
    {
        return sprintf('%s:%s', $config->getView(), $config->name);
    }
}
