<?php

namespace spec\Knp\RadBundle\Routing\Builder;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MethodPartBuilderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Knp\RadBundle\Routing\Builder\MethodPartBuilder');
    }
}
