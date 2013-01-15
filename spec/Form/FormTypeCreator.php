<?php

namespace spec\Knp\RadBundle\Form;

use PHPSpec2\ObjectBehavior;

class FormTypeCreator extends ObjectBehavior
{
    /**
     * @param Knp\RadBundle\Reflection\ClassMetadataFetcher $fetcher
     * @param Symfony\Component\Form\FormRegistryInterface $formRegistry
     * @param Symfony\Component\Form\FormFactoryInterface $factory
     */
    function let($fetcher, $factory, $formRegistry)
    {
        $this->beConstructedWith($fetcher, $factory, $formRegistry, 'App');
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
        $formRegistry->hasType('app.form.potato_type')->willReturn(false);
        $fetcher->getClass($object)->willReturn('App\Entity\Potato');
        $fetcher->getParentClass('App\Entity\Potato')->willReturn(null);

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
        $formRegistry->hasType('app.form.cheese_type')->willReturn(true);
        $fetcher->getClass($object)->willReturn('App\Entity\Cheese');
        $factory->create('app.form.cheese_type', $object, array())->shouldBeCalled()->willReturn($form);

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
        $formRegistry->hasType('app.form.edit_cheese_type')->willReturn(true);
        $formRegistry->hasType('app.form.cheese_type')->shouldNotBeCalled();
        $fetcher->getClass($object)->willReturn('App\Entity\Cheese');
        $factory->create('app.form.edit_cheese_type', $object, array())->shouldBeCalled()->willReturn($form);

        $this->create($object, 'edit')->shouldReturn($form);
    }

    /**
     * @param stdClass $object
     * @param stdClass $formType
     * @param Symfony\Component\Form\Form $form
     */
    function it_should_fallback_on_default_form_type_if_given_purpose_has_no_associated_form_type($object, $fetcher, $factory, $form, $formRegistry)
    {
        $fetcher->getClass($object)->willReturn('App\Entity\Cheese');
        $fetcher->getParentClass('App\\Entity\\Cheese')->willReturn(null);
        $fetcher->getShortClassName($object)->willReturn('Cheese');
        $fetcher->getShortClassName('App\Entity\Cheese')->willReturn('Cheese');
        $formRegistry->hasType('app.form.edit_cheese_type')->willReturn(false);
        $formRegistry->hasType('app.form.cheese_type')->willReturn(true);
        $factory->create('app.form.cheese_type', $object, array())->shouldBeCalled()->willReturn($form);

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
        $fetcher->getShortClassName('App\Entity\Cheese')->willReturn('Cheese');
        $fetcher->getClass('App\Entity\Cheese')->willReturn('App\Entity\Cheese');
        $fetcher->getClass($object)->willReturn('App\Entity\Cheese');
        $fetcher->getParentClass('App\Entity\Cheese')->willReturn(null);
        $formRegistry->hasType('app.form.cheese_type')->willReturn(false);
        $formRegistry->hasType('app.form.edit_cheese_type')->willReturn(false);

        $this->create($object, 'edit')->shouldReturn(null);
    }

    /**
     * @param stdClass $object
     * @param stdClass $formType
     * @param Symfony\Component\Form\Form $form
     */
    function it_should_get_form_for_other_rad_bundle_name($object, $fetcher, $factory, $formType, $form, $formRegistry)
    {
        $this->beConstructedWith($fetcher, $factory, $formRegistry, 'TestBundle');
        $fetcher->getShortClassName($object)->willReturn('Cheese');
        $fetcher->getShortClassName('TestBundle\Entity\Cheese')->willReturn('Cheese');
        $fetcher->getClass('TestBundle\Entity\Cheese')->willReturn('TestBundle\Entity\Cheese');
        $fetcher->getClass($object)->willReturn('TestBundle\Entity\Cheese');
        $fetcher->getParentClass('TestBundle\Entity\Cheese')->willReturn(null);
        $formRegistry->hasType('app.form.cheese_type')->willReturn(false);
        $formRegistry->hasType('app.form.edit_cheese_type')->willReturn(false);

        $this->create($object, 'edit')->shouldReturn(null);
    }
}
