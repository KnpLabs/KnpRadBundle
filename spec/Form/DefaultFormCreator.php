<?php

namespace spec\Knp\RadBundle\Form;

use PHPSpec2\ObjectBehavior;

class DefaultFormCreator extends ObjectBehavior
{
    /**
     * @param Knp\RadBundle\Reflection\ClassMetadataFetcher $fetcher
     * @param Symfony\Component\Form\FormFactoryInterface $factory
     */
    function let($fetcher, $factory)
    {
        $this->beConstructedWith($fetcher, $factory);
    }

    /**
     * @param stdClass $object
     * @param Symfony\Component\Form\FormFactoryInterface $factory
     * @param Knp\RadBundle\Form\ClassMetadataFetcher $fetcher
     * @param Symfony\Component\Form\FormBuilder $builder
     * @param Symfony\Component\Form\Form $form
     */
    function it_should_create_form_based_on_object_mutators($object, $factory, $fetcher, $builder, $form)
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
        $builder->add('name')->shouldBeCalled();
        $builder->add('admin')->shouldBeCalled();
        $builder->add('id')->shouldNotBeCalled();
        $builder->getForm()->willReturn($form);

        $this->create($object)->shouldReturn($form);
    }

    /**
     * @param stdClass $object
     * @param Symfony\Component\Form\FormFactoryInterface $factory
     * @param Knp\RadBundle\Form\ClassMetadataFetcher $fetcher
     * @param Symfony\Component\Form\FormBuilder $builder
     * @param Symfony\Component\Form\Form $form
     */
    public function it_should_create_form_based_on_object_properties($object, $factory, $fetcher, $builder, $form)
    {
        $fetcher->getProperties($object)->willReturn(array(
                'termOfService', 'locked',
        ));
        $factory->createBuilder('form', $object, array())->willReturn($builder)->shouldBeCalled();
        $builder->add('termOfService')->shouldBeCalled();
        $builder->add('locked')->shouldBeCalled();
        $builder->getForm()->willReturn($form);

        $this->create($object)->shouldReturn($form);
    }
}
