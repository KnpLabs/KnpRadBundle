<?php

namespace spec\Knp\RadBundle\Flash;

use PHPSpec2\ObjectBehavior;

class MessageRenderer extends ObjectBehavior
{
    /**
     * @param  Knp\RadBundle\Flash\Message                       $message
     * @param  Symfony\Component\Translation\TranslatorInterface $translator
     */
    function let($message, $translator)
    {
        $this->beConstructedWith($translator, 'default_catalog');

        $message->getTemplate()->willReturn('Hello {{ name }}!');
        $message->getParameters()->willReturn(array('{{ name }}' => 'George'));
        $message->getPluralization()->willReturn(123);
    }

    function it_should_translate_the_message_using_the_default_catalog($message, $translator)
    {
        $translator
            ->trans('Hello {{ name }}!', array('{{ name }}' => 'George'), 'default_catalog', 123)
            ->shouldBeCalled()
            ->willReturn('Bonjour George !')
        ;

        $this->render($message)->shouldReturn('Bonjour George !');
    }

    function it_should_use_the_specified_translations_catalog($message, $translator)
    {
        $translator
            ->trans(ANY_ARGUMENT, ANY_ARGUMENT, 'custom_catalog', ANY_ARGUMENT)
            ->shouldBeCalled()
            ->willReturn('Bonjour George !')
        ;

        $this->render($message, 'custom_catalog')->shouldReturn('Bonjour George !');
    }

    function it_should_use_the_messages_catalog_by_default($message, $translator)
    {
        $this->beConstructedWith($translator);

        $translator
            ->trans(ANY_ARGUMENT, ANY_ARGUMENT, 'messages', ANY_ARGUMENT)
            ->shouldBeCalled()
            ->willReturn('Bonjour George !')
        ;

        $this->render($message)->shouldReturn('Bonjour George !');
    }
}
