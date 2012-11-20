<?php

namespace Knp\RadBundle\DataFixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture as BaseAbstractFixture;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractFixture extends BaseAbstractFixture implements ContainerAwareInterface
{
    protected $container;

    public function createObjectFactory(ObjectManager $manager, $className)
    {
        return new ObjectFactory($this->referenceRepository, new ReferenceManipulator($this->referenceRepository), $manager, $className);
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
