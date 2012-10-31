<?php

namespace Knp\RadBundle\Finder;

use Symfony\Component\Finder\Finder;

class ClassFinder
{
    private $finder;

    public function __construct(Finder $finder = null)
    {
        $this->finder = $finder ?: new Finder();
    }

    public function findClasses($directory, $namespace)
    {
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
