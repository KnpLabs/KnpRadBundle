<?php

namespace Knp\RadBundle\Routing\ServiceBased\Factory;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class Expression
{
    private $factory;

    public function __construct(ArrayConfig $factory = null)
    {
        $this->factory = $factory ?: new ArrayConfig;
    }

    public function create($id, $expr)
    {
        $evaluator = new ExpressionLanguage;
        $config = $evaluator->evaluate($expr);

        return $this->factory->create($id, $config);
    }
}
