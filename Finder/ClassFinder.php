<?php

namespace Knp\RadBundle\Finder;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

class ClassFinder
{
    private $finder;
    private $filesystem;

    public function __construct(Finder $finder = null, Filesystem $filesystem = null)
    {
        $this->finder = $finder ?: new Finder();
        $this->filesystem = $filesystem ?: new Filesystem();
    }

    public function findClasses($directory, $namespace)
    {
        if (false === $this->filesystem->exists($directory)) {
            return array();
        }

        $classes = array();

        $this->finder->files();
        $this->finder->name('*.php');
        $this->finder->in($directory);

        foreach ($this->finder->getIterator() as $name) {
            $baseName = substr($name, strlen($directory)+1, -4);
            $baseClassName = str_replace('/', '\\', $baseName);

            $classes[] = $namespace.'\\'.$baseClassName;
        }

        return $classes;
    }

    public function findClassesMatching($directory, $namespace, $pattern)
    {
        $pattern = sprintf('#%s#', str_replace('#', '\#', $pattern));
        $matches = function ($path) use ($pattern) { return preg_match($pattern, $path); };

        return array_values(array_filter($this->findClasses($directory, $namespace), $matches));
    }
}
