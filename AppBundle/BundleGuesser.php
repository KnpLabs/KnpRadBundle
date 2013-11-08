<?php

namespace Knp\RadBundle\AppBundle;

use Symfony\Component\HttpKernel\KernelInterface;
use Knp\RadBundle\Reflection\ReflectionFactory;

class BundleGuesser
{
    private $kernel;
    private $reflectionFactory;
    private $bundleNames;

    public function __construct(KernelInterface $kernel, ReflectionFactory $reflectionFactory, array $bundleNames)
    {
        $this->kernel = $kernel;
        $this->reflectionFactory = $reflectionFactory;
        $this->bundleNames = $bundleNames;
    }

    public function getBundleForClass($class)
    {
        $reflectionClass = $this->reflectionFactory->createReflectionClass($class);
        $bundles = $this->kernel->getBundles();

        do {
            $namespace = $reflectionClass->getNamespaceName();
            foreach ($bundles as $bundle) {
                if (!in_array($bundle->getName(), $this->bundleNames)) {
                    continue;
                }
                if (0 === strpos($namespace, $bundle->getNamespace())) {
                    return $bundle;
                }
            }
            $reflectionClass = $reflectionClass->getParentClass();
        } while ($reflectionClass);

        throw new \InvalidArgumentException(sprintf(
            'The "%s" class does not belong to a registered bundle.',
            $class
        ));
    }

    public function hasBundleForClass($class)
    {
        try {
            $this->getBundleForClass($class);
        } catch (\InvalidArgumentException $e) {
            return false;
        }

        return true;
    }
}
