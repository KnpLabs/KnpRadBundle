<?php

namespace Knp\RadBundle\DataFixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture as BaseAbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Nelmio\Alice\Fixtures;

abstract class AbstractFixture extends BaseAbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    protected $container;

    public function load(ObjectManager $manager)
    {
        foreach ($this->getAliceFiles() as $files) {
            Fixtures::load($file, $manager, $this->getAliceOptions());
        }
    }

    public function getOrder()
    {
        return 1;
    }

    public function createObjectFactory(ObjectManager $manager, $className)
    {
        return new ObjectFactory($this->referenceRepository, new ReferenceManipulator($this->referenceRepository), $manager, $className);
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    protected function getAliceFiles()
    {
        return glob(__DIR__.'/*.yml');
    }

    protected function getAliceOptions()
    {
        return array(
            'providers' => array($this)
        );
    }
}
