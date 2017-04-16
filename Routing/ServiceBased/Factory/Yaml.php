<?php

namespace Knp\RadBundle\Routing\ServiceBased\Factory;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Yaml\Yaml as Parser;

class Yaml
{
    private $factory;

    public function __construct(ArrayConfig $factory = null)
    {
        $this->factory = $factory ?: new ArrayConfig;
    }

    public function create($id, $yaml)
    {
        return $this->factory->create($id, Parser::parse($yaml));
    }
}
