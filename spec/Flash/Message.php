<?php

namespace spec\Knp\RadBundle\Flash;

use PHPSpec2\ObjectBehavior;

class Message extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Hello {{ name }}!', array('{{ name }}' => 'George'), 123);
    }

    function it_should_have_a_template_accessor()
    {
        $this->getTemplate()->shouldReturn('Hello {{ name }}!');
    }

    function it_should_have_a_parameters_accessor()
    {
        $this->getParameters()->shouldReturn(array('{{ name }}' => 'George'));
    }

    function it_should_have_a_pluralization_accessor()
    {
        $this->getPluralization()->shouldReturn(123);
    }

    function it_should_use_null_as_default_pluralization()
    {
        $this->beConstructedWith('Hello {{ name }}!', array('{{ name }}' => 'George'));

        $this->getPluralization()->shouldReturn(null);
    }

    function it_should_use_empty_array_as_default_parameters()
    {
        $this->beConstructedWith('Hello {{ name }}!');

        $this->getParameters()->shouldReturn(array());
    }

    function it_should_have_a_string_representation_with_replaced_parameter_holders()
    {
        $this->beConstructedWith('Hello %name%!', array('%name%' => 'John'));

        $this->__toString()->shouldReturn('Hello John!');
    }
}
