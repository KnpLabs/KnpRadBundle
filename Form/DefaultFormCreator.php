<?php

namespace Knp\RadBundle\Form;

use Symfony\Component\Form\FormFactoryInterface;

class DefaultFormCreator
{
    private $factory;

    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function buildFormForObject($entity, $options)
    {
        $builder = $this->factory->createBuilder('form', $entity, $options);
        $class = new \ReflectionClass(get_class($entity));

        foreach ($class->getMethods() as $method) {
            if (0 === strpos($method->name, 'get')) {
                $propertyName = strtolower(substr($method->name, 3));
                if ($class->hasMethod('set'.ucfirst($propertyName))) {
                    $builder->add($propertyName);
                }
            }
        }

        return $builder->getForm();
    }
}
