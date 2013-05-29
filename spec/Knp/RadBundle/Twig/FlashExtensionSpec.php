<?php

namespace spec\Knp\RadBundle\Twig;

use PhpSpec\ObjectBehavior;

class FlashExtensionSpec extends ObjectBehavior
{
    /**
     * @param  Knp\RadBundle\Flash\Message                                      $flash
     * @param  Knp\RadBundle\Flash\MessageRenderer                              $renderer
     * @param  Symfony\Component\HttpFoundation\Session\Session                 $session
     * @param  Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface $flashes
     */
    function let($renderer, $session, $flashes)
    {
        $this->beConstructedWith($renderer, $session);

        $session->getFlashBag()->willReturn($flashes);
    }

    function it_should_be_a_twig_extension()
    {
        $this->shouldBeAnInstanceOf('Twig_ExtensionInterface');
    }

    function it_should_be_named_flash()
    {
        $this->getName()->shouldReturn('flash');
    }

    function it_should_register_a_flashes_token_parser()
    {
        $parsers = $this->getTokenParsers();
        $parsers->shouldHaveCount(1);
        $parsers[0]->shouldBeAnInstanceOf('Knp\RadBundle\Twig\FlashesTokenParser');
    }

    function its_renderMessage_should_render_a_message_instance($renderer, $flash)
    {
        $renderer->render($flash, null)->shouldBeCalled()->willReturn('Rendered flash');

        $this->renderMessage($flash)->shouldReturn('Rendered flash');
    }

    function its_renderMessage_should_allow_to_specify_a_custom_catalog($renderer, $flash)
    {
        $renderer->render($flash, 'custom_catalog')->shouldBeCalled()->willReturn('Rendered flash');

        $this->renderMessage($flash, 'custom_catalog')->shouldReturn('Rendered flash');
    }

    function its_renderMessage_should_not_change_non_message_values($renderer)
    {
        $renderer->render(\Prophecy\Argument::cetera())->shouldNotBeCalled();

        $this->renderMessage('Some string')->shouldReturn('Some string');
    }

    function its_getFlashes_should_return_all_the_flashes($flashes)
    {
        $flashes->all()->shouldBeCalled()->willReturn(array('success' => array('some success')));

        $this->getFlashes()->shouldReturn(array('success' => array('some success')));
    }

    function its_getFlashes_should_allow_to_specify_a_single_type($flashes)
    {
        $flashes->all()->shouldNotBeCalled();
        $flashes->get('success', array())->shouldBeCalled()->willReturn(array('first success', 'second success'));

        $this->getFlashes('success')->shouldReturn(array(
            'success' => array('first success', 'second success'),
        ));
    }

    function its_getFlashes_should_allow_to_specify_an_array_of_types($flashes)
    {
        $flashes->all()->shouldNotBeCalled();
        $flashes->get('success', array())->shouldBeCalled()->willReturn(array('first success', 'second success'));
        $flashes->get('failure', array())->shouldBeCalled()->willReturn(array('first failure', 'second failure'));

        $this->getFlashes(array('success', 'failure'))->shouldReturn(array(
            'success' => array('first success', 'second success'),
            'failure' => array('first failure', 'second failure')
        ));
    }
}
