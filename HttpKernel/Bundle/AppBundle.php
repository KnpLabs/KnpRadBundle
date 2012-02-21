<?php

/*
 * This file is part of the KnpRadBundle package.
 *
 * (c) KnpLabs <http://knplabs.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Knp\Bundle\RadBundle\HttpKernel\Bundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Knp\Bundle\RadBundle\DependencyInjection\Extension\AppExtension;

/**
 * Application bundle.
 *
 * Each application have only one application bundle.
 * It's the place of all application-related code.
 */
class AppBundle extends Bundle
{
    protected $name;
    protected $parent;
    protected $namespace;
    protected $rootDir;
    protected $path;
    protected $extension;

    /**
     * Initializes bundle.
     *
     * @param string      $namespace Application namespace
     * @param string      $rootDir   Src dir path
     * @param string|null $parent    Parent bundle name (if has one)
     */
    public function __construct($namespace, $rootDir, $parent = null)
    {
        $this->namespace = $namespace;
        $this->rootDir   = $rootDir;
        $this->name      = 'App';
        $this->parent    = $parent;
    }

    /**
     * Returns the application's container extension.
     *
     * If application has DependencyInjection\{APP_NAME}Extension in
     * it - it will be loaded. AppExtension will be loaded otherwise.
     *
     * @return ExtensionInterface The container extension
     */
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $class = $this->getNamespace().'\\DependencyInjection\\'.$this->getName().'Extension';
            if (class_exists($class)) {
                $this->extension = new $class();
            } else {
                $this->extension = new AppExtension($this->getPath());
            }
        }

        if ($this->extension) {
            return $this->extension;
        }
    }

    /**
     * Gets the application namespace.
     *
     * @return string The application namespace
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Gets the application directory path.
     *
     * @return string The application absolute path
     */
    public function getPath()
    {
        if (null === $this->path) {
            $this->path = sprintf('%s/%s',
                $this->rootDir, str_replace('\\', DIRECTORY_SEPARATOR, $this->getNamespace())
            );
        }

        return $this->path;
    }

    /**
     * Returns the bundle parent name.
     *
     * @return string The Bundle parent name it overrides or null if no parent
     */
    public function getParent()
    {
        return $this->parent;
    }
}
