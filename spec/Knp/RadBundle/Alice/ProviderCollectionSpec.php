<?php

namespace spec\Knp\RadBundle\Alice;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ProviderCollectionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Knp\RadBundle\Alice\ProviderCollection');
    }

    function it_returns_added_providers($provider1, $provider2)
    {
        $this->addProvider($provider1);
        $this->getProviders()->shouldReturn(array($provider1));

        $this->addProvider($provider2);
        $this->getProviders()->shouldReturn(array($provider1, $provider2));
    }
}
