<?php

namespace Knp\RadBundle\Routing\Loader;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Config\Resource\FileResource;
use Knp\RadBundle\Routing\Loader\ConventionalLoader\CollectionGeneratorInterface;
use Knp\RadBundle\Routing\Loader\YamlParser;

class ConventionalLoader implements LoaderInterface
{
    private $classicalLoader;
    private $collectionGenerator;
    private $locator;
    private $yaml;

    public function __construct(FileLocatorInterface $locator, CollectionGeneratorInterface $collectionGenerator,  YamlFileLoader $classicalLoader, YamlParser $yaml = null
    )
    {
        $this->locator = $locator;
        $this->collectionGenerator = $collectionGenerator;
        $this->classicalLoader = $classicalLoader;
        $this->yaml = $yaml ?: new YamlParser;
    }

    public function supports($resource, $type = null)
    {
        return 'rad_convention' === $type;
    }

    public function load($file, $type = null)
    {
        $path   = $this->locator->locate($file);
        $config = $this->yaml->parse($path);

        $collection = new RouteCollection;
        $collection->addResource(new FileResource($file));

        if (null === $config) {
            return $collection;
        }

        if (!is_array($config)) {
            throw new \InvalidArgumentException(sprintf(
                'The file "%s" must contain a YAML array.', $path
            ));
        }

        foreach ($config as $shortname => $mapping) {
            $parts = explode(':', $shortname);

            if (1 == count($parts)) {
                // @TODO @FIXME Symfony's YamlFileLoader is unusable in composition :(
                $tempName = tempnam(sys_get_temp_dir(), 'routing-yml');
                $this->yaml->dump(array($shortname => $mapping), $tempName);
                $collection->addCollection($this->classicalLoader->load($tempName, $type));

                continue;
            }

            if (2 == count($parts)) {
                $collection->addCollection($this->collectionGenerator->generate($shortname, $mapping));

                continue;
            }

            if (3 == count($parts)) {
                $collection->addCollection($this->collectionGenerator->generateRoute($shortname, $mapping));
                continue;
            }

        }

        return $collection;
    }

    public function getResolver()
    {
        return $this->classicalLoader->getResolver();
    }

    public function setResolver(LoaderResolverInterface $resolver)
    {
        return $this->classicalLoader->setResolver($resolver);
    }
}
