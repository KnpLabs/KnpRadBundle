<?php

namespace Knp\RadBundle\Form;

use Symfony\Component\Form\FormFactoryInterface;

class FormCreator implements FormCreatorInterface
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
            if (0 === strpos($method, 'get')) {
                $this->addField($builder, $object, strtolower(substr($method, 3)));
            }
            if (0 === strpos($method, 'is')) {
                $this->addField($builder, $object, strtolower(substr($method, 2)));
            }
        }

        return $builder->getForm();
    }

    private function addField($builder, $object, $propertyName)
    {
        if ($this->fetcher->hasMethod($object, 'set'.ucfirst($propertyName))) {
            $builder->add($propertyName);
        }
    }
}
