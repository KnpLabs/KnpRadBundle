<?php

namespace spec\Knp\RadBundle\HttpFoundation;

use PhpSpec\ObjectBehavior;

class RequestManipulatorSpec extends ObjectBehavior
{
    /**
     * @param Symfony\Component\HttpFoundation\Request $request
     * @param Symfony\Component\HttpFoundation\ParameterBag $attributesBag
     */
    function let($request, $attributesBag, $queryBag, $requestBag)
    {
        $request->getWrappedObject()->attributes = $attributesBag->getWrappedObject();
    }

    function it_should_get_an_attribute_from_the_given_request($request, $attributesBag)
    {
        $attributesBag->get('foo')->willReturn('The foo value');

        $this->getAttribute($request, 'foo')->shouldReturn('The foo value');
    }

    function it_should_indicate_whether_the_given_request_has_a_specific_attribute($request, $attributesBag)
    {
        $attributesBag->has('foo')->willReturn(false);

        $this->hasAttribute($request, 'foo')->shouldReturn(false);
    }

    function it_should_set_an_attribute_from_the_given_request($request, $attributesBag)
    {
        $attributesBag->set('foo', 'The new foo')->shouldBeCalled();

        $this->setAttribute($request, 'foo', 'The new foo');
    }
}
