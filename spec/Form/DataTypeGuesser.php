<?php

namespace spec\Knp\RadBundle\Form;

use PHPSpec2\ObjectBehavior;

class DataTypeGuesser extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Knp\RadBundle\Form\DataTypeGuesser');
    }

    function it_should_guess_form_type_given_internal_data_type()
    {
        $this->setData((object) array(
            'test' => true,
            'coll' => array(),
        ));

        $guess = $this->guessType('', 'test');
        $guess->getType()->shouldBe('checkbox');

        $guess = $this->guessType('', 'coll');
        $guess->getType()->shouldBe('collection');

        $guess = $this->guessType('', 'inexistant')->shouldReturn(null);
    }

    function it_should_guess_date_fields()
    {
        $this->setData((object) array(
            'date' => new \DateTime,
        ));

        $guess = $this->guessType('', 'date');
        $guess->getType()->shouldBe('date');
    }
}
