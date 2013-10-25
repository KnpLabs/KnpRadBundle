<?php

namespace spec\Knp\RadBundle\Resource;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RadResourceActionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('some_action_name');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Knp\RadBundle\Resource\RadResourceAction');
    }
}
