<?php

namespace spec\Knp\RadBundle\Form;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FormTypeCreatorSpec extends ObjectBehavior
{
    /**
     * @param Knp\RadBundle\Reflection\ClassMetadataFetcher $fetcher
     * @param Symfony\Component\Form\FormRegistryInterface $formRegistry
     * @param Symfony\Component\Form\FormFactoryInterface $factory
     * @param Knp\RadBundle\AppBundle\BundleGuesser $bundleGuesser
     * @param Symfony\Component\HttpKernel\Bundle\BundleInterface $bundle
     */
    function let($fetcher, $factory, $formRegistry, $bundleGuesser, $bundle)
    {
        $bundleGuesser->getBundleForClass(Argument::any())->willReturn($bundle);

        $bundle->getNamespace()->willReturn('Star\\Bundle\\CraftBundle');
        $bundle->getName()->willReturn('StarCraftBundle');

        $this->beConstructedWith($fetcher, $factory, $formRegistry, $bundleGuesser);
    }

    function it_should_implement_form_creator_interface()
    {
        $this->shouldBeAnInstanceOf('Knp\\RadBundle\\Form\\FormCreatorInterface');
    }

    /**
     * @param stdClass $object
     */
    function it_should_return_null_if_there_is_no_form_type($object, $fetcher, $formRegistry)
    {
        $fetcher->getShortClassName($object)->willReturn('Orc');
        $formRegistry->hasType('starcraft_orc')->willReturn(false);
        $fetcher->getClass($object)->willReturn('Star\\Bundle\\CraftBundle\\Entity\\Orc');
        $fetcher->getParentClass('Star\\Bundle\\CraftBundle\\Entity\\Orc')->willReturn(null);

        $this->create($object)->shouldReturn(null);
    }

    /**
     * @param stdClass $object
     * @param stdClass $formType
     * @param Symfony\Component\Form\Form $form
     */
    function it_should_return_form_type_if_there_is_one($object, $fetcher, $factory, $form, $formRegistry)
    {
        $fetcher->getShortClassName($object)->willReturn('Zerg');
        $formRegistry->hasType('starcraft_zerg')->willReturn(true);
        $fetcher->getClass($object)->willReturn('Star\\Bundle\\CraftBundle\\Entity\\Zerg');
        $factory->create('starcraft_zerg', $object, array())->shouldBeCalled()->willReturn($form);

        $this->create($object)->shouldReturn($form);
    }

    /**
     * @param stdClass $object
     * @param stdClass $formType
     * @param Symfony\Component\Form\Form $form
     */
    function it_should_return_form_type_with_purpose_if_there_is_one($object, $fetcher, $factory, $form, $formRegistry)
    {
        $fetcher->getShortClassName($object)->willReturn('Zerg');
        $formRegistry->hasType('starcraft_edit_zerg')->willReturn(true);
        $formRegistry->hasType('starcraft_zerg')->shouldNotBeCalled();
        $fetcher->getClass($object)->willReturn('Star\\Bundle\\CraftBundle\\Entity\\Zerg');
        $factory->create('starcraft_edit_zerg', $object, array())->shouldBeCalled()->willReturn($form);

        $this->create($object, 'edit')->shouldReturn($form);
    }

    /**
     * @param stdClass $object
     * @param stdClass $formType
     * @param Symfony\Component\Form\Form $form
     */
    function it_should_fallback_on_default_form_type_if_given_purpose_has_no_associated_form_type($object, $fetcher, $factory, $form, $formRegistry)
    {
        $fetcher->getClass($object)->willReturn('Star\\Bundle\\CraftBundle\\Entity\\Zerg');
        $fetcher->getParentClass('Star\\Bundle\\CraftBundle\\Entity\\Zerg')->willReturn(null);
        $fetcher->getShortClassName($object)->willReturn('Zerg');
        $fetcher->getShortClassName('Star\\Bundle\\CraftBundle\\Entity\\Zerg')->willReturn('Zerg');
        $formRegistry->hasType('starcraft_edit_zerg')->willReturn(false);
        $formRegistry->hasType('starcraft_zerg')->willReturn(true);
        $factory->create('starcraft_zerg', $object, array())->shouldBeCalled()->willReturn($form);

        $this->create($object, 'edit')->shouldReturn($form);
    }
}
