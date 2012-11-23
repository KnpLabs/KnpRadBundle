<?php

namespace spec\Knp\RadBundle\DataFixtures;

use PHPSpec2\ObjectBehavior;

class ReferenceManipulator extends ObjectBehavior
{
    /**
     * @param Doctrine\Common\DataFixtures\ReferenceRepository $referenceRepository
     */
    function let($referenceRepository)
    {
        $this->beConstructedWith($referenceRepository);
    }

    function it_should_generate_a_reference_name_based_on_classname_and_first_attribute_name($referenceRepository)
    {
        $referenceRepository->hasReference('Cheese:Camembert')->shouldBeCalled()->willReturn(false);

        $this->createReferenceName('App\Entity\Cheese', array('name' => 'Camembert', 'region' => 'Normandie'))->shouldReturn('Cheese:Camembert');
    }

    function it_should_generate_an_incremented_alternative_reference_name_if_default_one_already_exists($referenceRepository)
    {
        $referenceRepository->hasReference('Cheese:Camembert')->shouldBeCalled()->willReturn(true);
        $referenceRepository->hasReference('Cheese:Camembert-1')->shouldBeCalled()->willReturn(true);
        $referenceRepository->hasReference('Cheese:Camembert-2')->shouldBeCalled()->willReturn(false);

        $this->createReferenceName('App\Entity\Cheese', array('name' => 'Camembert', 'region' => 'Normandie'))->shouldReturn('Cheese:Camembert-2');
    }

    function it_should_generate_an_alternative_reference_name_if_no_attributes_were_given($referenceRepository)
    {
        $referenceRepository->hasReference('Cheese')->shouldBeCalled()->willReturn(false);

        $this->createReferenceName('App\Entity\Cheese', array())->shouldReturn('Cheese');
    }

    function it_should_generate_an_incremented_alternative_reference_name_if_no_attributes_were_given($referenceRepository)
    {
        $referenceRepository->hasReference('Cheese')->shouldBeCalled()->willReturn(true);
        $referenceRepository->hasReference('Cheese:1')->shouldBeCalled()->willReturn(true);
        $referenceRepository->hasReference('Cheese:2')->shouldBeCalled()->willReturn(false);

        $this->createReferenceName('App\Entity\Cheese', array())->shouldReturn('Cheese:2');
    }
}
