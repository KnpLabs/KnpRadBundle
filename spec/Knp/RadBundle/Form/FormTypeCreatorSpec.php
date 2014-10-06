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

        $bundle->getNamespace()->willReturn('Star\Bundle\CraftBundle');
        $bundle->getName()->willReturn('StarCraftBundle');

        $this->beConstructedWith($fetcher, $factory, $formRegistry, $bundleGuesser);
    }

    function it_should_implement_form_creator_interface()
    {
        $this->shouldBeAnInstanceOf('Knp\RadBundle\Form\FormCreatorInterface');
    }

    /**
     * @param stdClass $object
     */
    function it_should_return_null_if_there_is_no_form_type($object, $fetcher, $formRegistry)
    {
        $fetcher->getShortClassName($object)->willReturn('Potato');
        $formRegistry->hasType('starcraft_potato')->willReturn(false);
        $fetcher->getClass($object)->willReturn('Star\Bundle\CraftBundle\Entity\Potato');
        $fetcher->getParentClass('Star\Bundle\CraftBundle\Entity\Potato')->willReturn(null);

        $this->create($object)->shouldReturn(null);
    }

    /**
     * @param stdClass $object
     * @param stdClass $formType
     * @param Symfony\Component\Form\Form $form
     */
    function it_should_return_form_type_if_there_is_one($object, $fetcher, $factory, $form, $formRegistry)
    {
        $fetcher->getShortClassName($object)->willReturn('Cheese');
        $formRegistry->hasType('starcraft_cheese')->willReturn(true);
        $fetcher->getClass($object)->willReturn('Star\Bundle\CraftBundle\Entity\Cheese');
        $factory->create('starcraft_cheese', $object, array())->shouldBeCalled()->willReturn($form);

        $this->create($object)->shouldReturn($form);
    }

    /**
     * @param stdClass $object
     * @param stdClass $formType
     * @param Symfony\Component\Form\Form $form
     */
    function it_should_return_form_type_with_purpose_if_there_is_one($object, $fetcher, $factory, $form, $formRegistry)
    {
        $fetcher->getShortClassName($object)->willReturn('Cheese');
        $formRegistry->hasType('starcraft_edit_cheese')->willReturn(true);
        $formRegistry->hasType('starcraft_cheese')->shouldNotBeCalled();
        $fetcher->getClass($object)->willReturn('Star\Bundle\CraftBundle\Entity\Cheese');
        $factory->create('starcraft_edit_cheese', $object, array())->shouldBeCalled()->willReturn($form);

        $this->create($object, 'edit')->shouldReturn($form);
    }

    /**
     * @param stdClass $object
     * @param stdClass $formType
     * @param Symfony\Component\Form\Form $form
     */
    function it_should_fallback_on_default_form_type_if_given_purpose_has_no_associated_form_type($object, $fetcher, $factory, $form, $formRegistry)
    {
        $fetcher->getClass($object)->willReturn('Star\Bundle\CraftBundle\Entity\Cheese');
        $fetcher->getParentClass('Star\Bundle\CraftBundle\Entity\Cheese')->willReturn(null);
        $fetcher->getShortClassName($object)->willReturn('Cheese');
        $fetcher->getShortClassName('Star\Bundle\CraftBundle\Entity\Cheese')->willReturn('Cheese');
        $formRegistry->hasType('starcraft_edit_cheese')->willReturn(false);
        $formRegistry->hasType('starcraft_cheese')->willReturn(true);
        $factory->create('starcraft_cheese', $object, array())->shouldBeCalled()->willReturn($form);

        $this->create($object, 'edit')->shouldReturn($form);
    }

    /**
     * @param stdClass $object
     * @param stdClass $formType
     * @param Symfony\Component\Form\Form $form
     */
    function it_should_return_null_if_given_purpose_has_no_associated_form_type_and_no_default_form_type($object, $fetcher, $factory, $formType, $form, $formRegistry)
    {
        $fetcher->getShortClassName($object)->willReturn('Cheese');
        $fetcher->getShortClassName('Star\Bundle\CraftBundle\Entity\Cheese')->willReturn('Cheese');
        $fetcher->getClass('Star\Bundle\CraftBundle\Entity\Cheese')->willReturn('Star\Bundle\Craft\Entity\Cheese');
        $fetcher->getClass($object)->willReturn('Star\Bundle\CraftBundle\Entity\Cheese');
        $fetcher->getParentClass('Star\Bundle\CraftBundle\Entity\Cheese')->willReturn(null);
        $formRegistry->hasType('starcraft_cheese')->willReturn(false);
        $formRegistry->hasType('starcraft_edit_cheese')->willReturn(false);

        $this->create($object, 'edit')->shouldReturn(null);
    }

    /**
     * @param stdClass $object
     * @param stdClass $formType
     * @param Symfony\Component\Form\Form $form
     */
    function it_should_get_form_for_other_rad_bundle_name($object, $fetcher, $factory, $formType, $form, $formRegistry, $bundle)
    {
        $bundle->getNamespace()->willReturn('App');
        $bundle->getName()->willReturn('App');
        $fetcher->getShortClassName($object)->willReturn('Cheese');
        $fetcher->getShortClassName('App\Entity\Cheese')->willReturn('Cheese');
        $fetcher->getClass('App\Entity\Cheese')->willReturn('App\Entity\Cheese');
        $fetcher->getClass($object)->willReturn('App\Entity\Cheese');
        $fetcher->getParentClass('App\Entity\Cheese')->willReturn(null);
        $formRegistry->hasType('app_edit_cheese')->willReturn(true);
        $factory->create('app_edit_cheese', $object, array())->shouldBeCalled()->willReturn($form);

        $this->create($object, 'edit')->shouldReturn($form);
    }
}
