<?php

namespace Knp\RadBundle\Routing\Conventional;

use Knp\RadBundle\Routing\Conventional\Config;

interface Generator
{
    public function generate(Config $config);
}
