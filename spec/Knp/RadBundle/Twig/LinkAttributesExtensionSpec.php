<?php

namespace spec\Knp\RadBundle\Twig;

use PhpSpec\ObjectBehavior;

class LinkAttributesExtensionSpec extends ObjectBehavior
{
    /**
     * @param Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface $csrfProvider
     */
    function let($csrfProvider)
    {
        $csrfProvider->generateCsrfToken('link')->willReturn('some token');

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
        $this->getFunctions()->shouldHaveCount(2);
    }

    function its_getLinkAttributes_should_return_html_attributes_for_delete_method_with_default_confirmation_message()
    {
        $this->getLinkAttributes('delete')->shouldReturn('data-method="delete" data-confirm="Are you sure?" data-csrf-token="some token"');
    }

    function its_getLinkAttributes_should_return_html_attributes_for_delete_method_with_a_specified_confirmation_message()
    {
        $this->getLinkAttributes('delete', 'a confirmation message')->shouldReturn('data-method="delete" data-confirm="a confirmation message" data-csrf-token="some token"');
    }

    function its_getLinkAttributes_should_return_html_attributes_for_delete_method_with_no_confirmation_attribute()
    {
        $this->getLinkAttributes('delete', false)->shouldReturn('data-method="delete" data-no-confirm data-csrf-token="some token"');
    }

    function its_getLinkAttributes_should_return_html_attributes_for_post_method()
    {
        $this->getLinkAttributes('post')->shouldReturn('data-method="post" data-confirm="Are you sure?" data-csrf-token="some token"');
    }

    function its_getLinkAttributes_should_return_html_attributes_for_post_method_with_a_specified_confirmation_message()
    {
        $this->getLinkAttributes('post', 'Please confirm')->shouldReturn('data-method="post" data-confirm="Please confirm" data-csrf-token="some token"');
    }

    function its_getLinkAttributes_should_return_html_attributes_for_put_method()
    {
        $this->getLinkAttributes('put')->shouldReturn('data-method="put" data-confirm="Are you sure?" data-csrf-token="some token"');
    }

    function its_getLinkAttributes_should_return_html_attributes_for_put_method_with_a_specified_confirmation_message()
    {
        $this->getLinkAttributes('put', 'Please confirm')->shouldReturn('data-method="put" data-confirm="Please confirm" data-csrf-token="some token"');
    }

    function its_getLinkAttributes_should_return_html_attributes_for_patch_method()
    {
        $this->getLinkAttributes('patch')->shouldReturn('data-method="patch" data-confirm="Are you sure?" data-csrf-token="some token"');
    }

    function its_getLinkAttributes_should_return_html_attributes_for_patch_method_with_a_specified_confirmation_message()
    {
        $this->getLinkAttributes('patch', 'Please confirm')->shouldReturn('data-method="patch" data-confirm="Please confirm" data-csrf-token="some token"');
    }

    function its_getLinkAttributes_should_return_html_attributes_for_specified_method_even_if_it_is_fancy()
    {
        $this->getLinkAttributes('fancy', 'Please confirm')->shouldReturn('data-method="fancy" data-confirm="Please confirm" data-csrf-token="some token"');
    }
}
