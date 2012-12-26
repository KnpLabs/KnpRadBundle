<?php

namespace Knp\RadBundle\DataFixtures\ORM;

use Knp\RadBundle\DataFixtures\AbstractFixture;
use Symfony\Component\Finder\Finder;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Fixtures;

class LoadAliceFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $bundles = $this->container->getParameter('kernel.bundles');
        if (!isset($bundles['App'])) {
            return;
        }

        $refl = new \ReflectionClass($bundles['App']);
        if (class_exists($refl->getNamespaceName().'\\DataFixtures\\ORM\\LoadAliceFixtures')) {
            return;
        }

        $path = dirname($refl->getFileName()).DIRECTORY_SEPARATOR.'Resources'.
            DIRECTORY_SEPARATOR.'fixtures'.DIRECTORY_SEPARATOR.'orm';

        foreach ($this->getAliceFiles($path) as $file) {
            Fixtures::load($file, $manager, $this->getAliceOptions());
        }
    }

    protected function getAliceFiles($path)
    {
        if (!is_dir($path)) {
            return array();
        }

        return Finder::create()->files()->name('*.yml')->in($path);
    }

    protected function getAliceOptions()
    {
        return array('providers' => array($this));
    }
}
