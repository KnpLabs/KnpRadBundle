<?php

namespace spec\Knp\RadBundle\Resource\Resolver;

use Prophecy\Argument;
use PhpSpec\ObjectBehavior;

class AggregateSpec extends ObjectBehavior
{
    /**
     * @param Knp\RadBundle\Resource\Resolver\ExpressionLanguageBased $exprBased
     * @param Knp\RadBundle\Resource\Resolver\OptionsBased $optionsBased
     * @param Symfony\Component\HttpFoundation\Request $request
     */
    function let($exprBased, $optionsBased)
    {
        $this->beConstructedWith($optionsBased, $exprBased);
    }

    function its_resolveResource_delegates_to_expr_resolver($request, $exprBased, $optionsBased)
    {
        $exprBased->resolveResource($request, array('expr' => ''))->shouldBeCalled();
        $optionsBased->resolveResource($request, Argument::any())->shouldNotBeCalled();
        $this->resolveResource($request, array('expr' => ''));
    }

    function its_resolveResource_delegates_to_options_resolver($request, $exprBased, $optionsBased)
    {
        $optionsBased->resolveResource($request, array('service' => '', 'method', 'arguments' => array()))->shouldBeCalled();
        $exprBased->resolveResource($request, Argument::any())->shouldNotBeCalled();
        $this->resolveResource($request, array('service' => '', 'method', 'arguments' => array()));
    }
}
