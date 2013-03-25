<?php

namespace spec\Knp\RadBundle\Form;

use PHPSpec2\ObjectBehavior;

class DefaultFormCreator extends ObjectBehavior
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
     * @param Knp\RadBundle\Form\ClassMetadataFetcher $fetcher
     * @param Symfony\Component\Form\FormBuilder $builder
     * @param Symfony\Component\Form\Form $form
     * @param Symfony\Component\Form\Form $subForm1
     * @param Symfony\Component\Form\Form $subForm2
     * @param Symfony\Component\Form\Event\DataEvent $dataEvent
     */
    function it_should_create_form_based_on_object_mutators($object, $factory, $fetcher, $builder, $form, $dataEvent, $subForm1, $subForm2)
    {
        $fetcher->getMethods($object)->willReturn(array(
                'getName', 'setName',
                'isAdmin', 'setAdmin',
                'getId', 'foo',
        ));
        $fetcher->hasMethod($object, 'setName')->willReturn(true);
        $fetcher->hasMethod($object, 'setAdmin')->willReturn(true);
        $fetcher->hasMethod($object, 'setId')->willReturn(false);
        $factory->createBuilder('form', $object, array())->willReturn($builder)->shouldBeCalled();

        $dataEvent->getData()->willReturn($object);
        $dataEvent->getForm()->willReturn($form);

        $factory->createForProperty(ANY_ARGUMENT, 'name')->shouldBeCalled()->willReturn($subForm1);
        $factory->createForProperty(ANY_ARGUMENT, 'admin')->shouldBeCalled()->willReturn($subForm2);
        $factory->createForProperty(ANY_ARGUMENT, 'id')->shouldNotBeCalled();

        $form->add($subForm1)->shouldBeCalled();
        $form->add($subForm2)->shouldBeCalled();

        $this->preSetData($dataEvent);

        $builder->getForm()->willReturn($form);

        $this->create($object)->shouldReturn($form);
    }

    /**
     * @param stdClass $object
     * @param Symfony\Component\Form\FormFactoryInterface $factory
     * @param Knp\RadBundle\Form\ClassMetadataFetcher $fetcher
     * @param Symfony\Component\Form\FormBuilder $builder
     * @param Symfony\Component\Form\Form $form
     * @param Symfony\Component\Form\Form $subForm1
     * @param Symfony\Component\Form\Form $subForm2
     * @param Symfony\Component\Form\Event\DataEvent $dataEvent
     */
    public function it_should_create_form_based_on_object_properties($object, $factory, $fetcher, $builder, $form, $dataEvent, $subForm1, $subForm2)
    {
        $fetcher->getProperties($object)->willReturn(array(
                'termOfService', 'locked',
        ));
        $factory->createBuilder('form', $object, array())->willReturn($builder)->shouldBeCalled();

        $dataEvent->getData()->willReturn($object);
        $dataEvent->getForm()->willReturn($form->getWrappedSubject());

        $factory->createForProperty(ANY_ARGUMENT, 'termOfService')->shouldBeCalled()->willReturn($subForm1);
        $factory->createForProperty(ANY_ARGUMENT, 'locked')->shouldBeCalled()->willReturn($subForm2);

        $form->add($subForm1)->shouldBeCalled();
        $form->add($subForm2)->shouldBeCalled();

        $this->preSetData($dataEvent);

        $builder->getForm()->willReturn($form);

        $this->create($object)->shouldReturn($form);
    }
}
