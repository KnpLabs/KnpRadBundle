<?php

namespace Knp\RadBundle\DataFixtures;

use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\Common\Persistence\ObjectManager;

class ObjectFactory
{
    private $manager;
    private $className;

    public function __construct(
        ReferenceRepository $referenceRepository,
        ReferenceManipulator $referenceManipulator,
        ObjectManager $manager,
        $className
    )
    {
        $this->referenceRepository  = $referenceRepository;
        $this->referenceManipulator = $referenceManipulator;
        $this->manager              = $manager;
        $this->className            = $className;
    }

    public function add(array $attributes = array())
    {
        $object = new $this->className();

        foreach ($attributes as $attribute => $value) {
            $object->{'set'.ucfirst($attribute)}($value);
        }

        $this->referenceRepository->addReference(
            $this->referenceManipulator->createReferenceName($this->className, $attributes),
            $object
        );

        $this->manager->persist($object);

        return $this;
    }
}
