<?php

namespace spec\Knp\RadBundle\Twig;

use PHPSpec2\ObjectBehavior;

class LinkAttributesExtension extends ObjectBehavior
{
    /**
     * @param Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface $csrfProvider
     */
    function let($csrfProvider)
    {
        $this->beConstructedWith($csrfProvider);
    }

    function it_should_be_a_twig_extension()
    {
        $this->shouldHaveType('Twig_Extension');
    }

    function it_should_have_a_name()
    {
        $this->getName()->shouldReturn('link_attributes');
    }

    function it_should_have_the_csrf_attribute_function()
    {
        $this->getFunctions()->shouldHaveCount(4);
    }

    function its_getDeleteAttributes_should_return_html_attributes_for_delete_method($csrfProvider)
    {
        $csrfProvider->generateCsrfToken('delete')->willReturn('some token');

        $this->getDeleteAttributes('a confirmation message')->shouldReturn('data-method="delete" data-confirm="a confirmation message" data-csrf-token="some token"');
    }

    function its_getDeleteAttributes_should_return_html_attributes_for_delete_method_with_no_confirmation_attribute(
        $csrfProvider
    )
    {
        $csrfProvider->generateCsrfToken('delete')->willReturn('some token');

        $this->getDeleteAttributes(false)->shouldReturn('data-method="delete" data-no-confirm data-csrf-token="some token"');
    }

    function its_getDeleteAttributes_should_return_html_attributes_for_delete_method_with_default_confirmation_message(
        $csrfProvider
    )
    {
        $csrfProvider->generateCsrfToken('delete')->willReturn('some token');

        $this->getDeleteAttributes()->shouldReturn('data-method="delete" data-confirm="Are you sure?" data-csrf-token="some token"');
    }

    function its_getPostAttributes_should_return_html_attributes_for_post_method($csrfProvider)
    {
        $csrfProvider->generateCsrfToken('post')->willReturn('some token');

        $this->getPostAttributes()->shouldReturn('data-method="post" data-csrf-token="some token"');
    }

    function its_getPutAttributes_should_return_html_attributes_for_post_method($csrfProvider)
    {
        $csrfProvider->generateCsrfToken('put')->willReturn('some token');

        $this->getPutAttributes()->shouldReturn('data-method="put" data-csrf-token="some token"');
    }

    function its_getPatchAttributes_should_return_html_attributes_for_post_method($csrfProvider)
    {
        $csrfProvider->generateCsrfToken('patch')->willReturn('some token');

        $this->getPatchAttributes()->shouldReturn('data-method="patch" data-csrf-token="some token"');
    }
}
