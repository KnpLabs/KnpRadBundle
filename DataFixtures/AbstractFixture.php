<?php

namespace Knp\RadBundle\DataFixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture as BaseAbstractFixture;

abstract class AbstractFixture extends BaseAbstractFixture
{
    private $referenceManipulator;

    public function createObject(ObjectManager $manager, $className, array $attributes = array())
    {
        $object = new $className();

        foreach ($attributes as $attribute => $value) {
            $object->{'set'.ucfirst($attribute)}($value);
        }

        $this->addReference($this->referenceManipulator->createReferenceName($className, $attributes), $object);

        $manager->persist($object);
    }
}
