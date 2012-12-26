<?php

namespace Knp\RadBundle\DataFixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture as BaseAbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractFixture extends BaseAbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    protected $container;

    public function getOrder()
    {
        return 0;
    }

    public function createObjectFactory(ObjectManager $manager, $className)
    {
        return new ObjectFactory($this->referenceRepository, new ReferenceManipulator($this->referenceRepository), $manager, $className);
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
