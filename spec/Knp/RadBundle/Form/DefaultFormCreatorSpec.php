<?php

namespace spec\Knp\RadBundle\Form;

use PhpSpec\ObjectBehavior;

class DefaultFormCreatorSpec extends ObjectBehavior
{
    /**
     * @param Knp\RadBundle\Reflection\ClassMetadataFetcher $fetcher
     * @param Symfony\Component\Form\FormFactoryInterface $factory
     * @param Knp\RadBundle\Form\DataTypeGuesser $dataTypeGuesser
     */
    function let($fetcher, $factory, $dataTypeGuesser)
    {
        $this->beConstructedWith($fetcher, $factory, $dataTypeGuesser);
    }

    /**
     * @param stdClass $object
     * @param Symfony\Component\Form\FormFactoryInterface $factory
     * @param Knp\RadBundle\Reflection\ClassMetadataFetcher $fetcher
     * @param Symfony\Component\Form\FormBuilder $builder
     * @param Symfony\Component\Form\Form $form
     * @param Symfony\Component\Form\Form $subForm1
     * @param Symfony\Component\Form\Form $subForm2
     * @param Symfony\Component\Form\FormEvent $formEvent
     */
    function it_should_create_form_based_on_object_mutators($object, $factory, $fetcher, $builder, $form, $formEvent, $subForm1, $subForm2)
    {
        $fetcher->getProperties($object)->willReturn(array());
        $fetcher->getMethods($object)->willReturn(array(
                'getName', 'setName',
                'isAdmin', 'setAdmin',
                'getId', 'foo',
        ));
        $fetcher->hasMethod($object, 'setName')->willReturn(true);
        $fetcher->hasMethod($object, 'setAdmin')->willReturn(true);
        $fetcher->hasMethod($object, 'setId')->willReturn(false);
        $factory->createBuilder('form', $object, array())->willReturn($builder)->shouldBeCalled();

        $formEvent->getData()->willReturn($object);
        $formEvent->getForm()->willReturn($form);

        $factory->createForProperty(\Prophecy\Argument::any(), 'name')->shouldBeCalled()->willReturn($subForm1);
        $factory->createForProperty(\Prophecy\Argument::any(), 'admin')->shouldBeCalled()->willReturn($subForm2);
        $factory->createForProperty(\Prophecy\Argument::any(), 'id')->shouldNotBeCalled();

        $form->add($subForm1)->shouldBeCalled();
        $form->add($subForm2)->shouldBeCalled();

        $this->preSetData($formEvent);

        $builder->addEventSubscriber($this)->shouldBeCalled();
        $builder->getForm()->willReturn($form);

        $this->create($object)->shouldReturn($form);
    }

    /**
     * @param stdClass $object
     * @param Symfony\Component\Form\FormFactoryInterface $factory
     * @param Knp\RadBundle\Reflection\ClassMetadataFetcher $fetcher
     * @param Symfony\Component\Form\FormBuilder $builder
     * @param Symfony\Component\Form\Form $form
     * @param Symfony\Component\Form\Form $subForm1
     * @param Symfony\Component\Form\Form $subForm2
     * @param Symfony\Component\Form\FormEvent $formEvent
     */
    public function it_should_create_form_based_on_object_properties($object, $factory, $fetcher, $builder, $form, $formEvent, $subForm1, $subForm2)
    {
        $fetcher->getMethods($object)->willReturn(array());
        $fetcher->getProperties($object)->willReturn(array(
                'termOfService', 'locked',
        ));
        $factory->createBuilder('form', $object, array())->willReturn($builder)->shouldBeCalled();

        $formEvent->getData()->willReturn($object);
        $formEvent->getForm()->willReturn($form->getWrappedObject());

        $factory->createForProperty(\Prophecy\Argument::any(), 'termOfService')->shouldBeCalled()->willReturn($subForm1);
        $factory->createForProperty(\Prophecy\Argument::any(), 'locked')->shouldBeCalled()->willReturn($subForm2);

        $form->add($subForm1)->shouldBeCalled();
        $form->add($subForm2)->shouldBeCalled();

        $this->preSetData($formEvent);

        $builder->getForm()->willReturn($form);
        $builder->addEventSubscriber($this)->shouldBeCalled();

        $this->create($object)->shouldReturn($form);
    }
}
