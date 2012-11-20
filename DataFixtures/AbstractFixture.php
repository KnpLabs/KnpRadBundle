<?php

namespace Knp\RadBundle\DataFixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture as BaseAbstractFixture;

abstract class AbstractFixture extends BaseAbstractFixture
{
    public function createObjectFactory(ObjectManager $manager, $className)
    {
        return new ObjectFactory($this->referenceRepository, new ReferenceManipulator($this->referenceRepository), $manager, $className);
    }
}
