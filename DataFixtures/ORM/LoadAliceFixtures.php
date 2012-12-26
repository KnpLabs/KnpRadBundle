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
        $env     = $this->continaer->getParameter('kernel.environment');
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

        foreach ($this->getAliceFiles($path, $env) as $file) {
            Fixtures::load($file, $manager, $this->getAliceOptions());
        }
    }

    protected function getAliceFiles($path, $environment)
    {
        $paths = array();
        if (is_dir($path)) {
            $paths[] = $path;
        }
        if (is_dir($path.DIRECTORY_SEPARATOR.$environment)) {
            $paths[] = $path.DIRECTORY_SEPARATOR.$environment;
        }

        if (0 == count($paths)) {
            return array();
        }

        return Finder::create()
            ->files()
            ->name('*.yml')
            ->depth(1)
            ->sortByName()
            ->in($paths)
        ;
    }

    protected function getAliceOptions()
    {
        return array('providers' => array($this));
    }
}
