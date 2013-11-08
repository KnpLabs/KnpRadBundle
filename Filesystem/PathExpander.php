<?php

namespace Knp\RadBundle\Filesystem;

use Symfony\Component\HttpKernel\KernelInterface;

class PathExpander
{
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * Expands the given path if needed (i.e bundle resource path)
     *
     * @param string $path The path to expand
     *
     * @return string The expanded path
     */
    public function expand($path)
    {
        if (0 !== strpos($path, '@')) {
            return $path;
        }

        list($bundleName, $relPath) = explode('/', substr($path, 1), 2);

        $bundle = $this->kernel->getBundle($bundleName);

        return sprintf('%s/%s', $bundle->getPath(), $relPath);
    }
}
