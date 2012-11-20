<?php

namespace Knp\RadBundle\DataFixtures;

use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\Common\Persistence\ObjectManager;

class ObjectFactory
{
    private $manager;
    private $className;
    private $defaultAttributes;

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
        // We do not override $attributes because the reference manipulator will use the first element to generate the reference name
        $mergedAttributes = array_merge($this->defaultAttributes, $attributes);
        $object = new $this->className();

        foreach ($mergedAttributes as $attribute => $value) {
            $object->{'set'.ucfirst($attribute)}($value);
        }

        $this->referenceRepository->addReference(
            $this->referenceManipulator->createReferenceName($this->className, $attributes),
            $object
        );

        $this->manager->persist($object);

        return $this;
    }

    public function setDefaults(array $attributes = array())
    {
        $this->defaultAttributes = $attributes;

        return $this;
    }
}
