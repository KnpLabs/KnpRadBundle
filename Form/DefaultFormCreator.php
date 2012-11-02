<?php

namespace Knp\RadBundle\Form;

use Symfony\Component\Form\FormFactoryInterface;
use Knp\RadBundle\Reflection\ClassMetadataFetcher;

class DefaultFormCreator implements FormCreatorInterface
{
    private $fetcher;
    private $factory;

    public function __construct(ClassMetadataFetcher $fetcher = null, FormFactoryInterface $factory)
    {
        $this->fetcher = $fetcher ?: new ClassMetadataFetcher;
        $this->factory = $factory;
    }

    public function create($object, $purpose = null, array $options = array())
    {
        $builder = $this->factory->createBuilder('form', $object, $options);

        foreach ($this->fetcher->getMethods($object) as $method) {
            if (0 === strpos($method, 'get') || 0 === strpos($method, 'is')) {
                $propertyName = $this->extractPropertyName($method);
                if ($this->hasRelatedSetter($object, $propertyName)) {
                    $builder->add($propertyName);
                }
            }
        }

        foreach ($this->fetcher->getProperties($object) as $property) {
            $builder->add($property);
        }

        return $builder->getForm();
    }

    private function extractPropertyName($methodName)
    {
        return lcfirst(preg_replace('#is|get#', '', $methodName));
    }

    private function hasRelatedSetter($object, $propertyName)
    {
        return $this->fetcher->hasMethod($object, 'set'.ucfirst($propertyName));
    }
}
