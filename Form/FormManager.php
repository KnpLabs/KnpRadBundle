<?php

namespace Knp\RadBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormFactoryInterface;

class FormManager
{
    private $factory;
    private $formCreator;

    public function __construct(FormFactoryInterface $factory, DefaultFormCreator $formCreator)
    {
        $this->factory     = $factory;
        $this->formCreator = $formCreator;
    }

    public function createFormFor($data, $name = null, array $options = array())
    {
        if (null !== $name) {
            // if a FormType is provided, we use it
            //TODO Check that FormType provided is compatible with $data class
            return $this->factory->create($this->getFormType($data, $name), $data, $options);
        }

        // We look for a corresponding FormType
        if (null === $formType = $this->getDefaultFormType($data)) {
            // if no form type is found, we create one by looking $data properties

            return $this->formCreator->buildFormForObject($data, $options);
        }

        // If one is found, we use it
        return $this->factory->create($formType, $data, $options);
    }

    private function getFormType($entity, $name = null)
    {
        $entityClass = is_object($entity) ? get_class($entity) : $entity;
        $arr = explode('\\', $entityClass);
        $formClass = sprintf('App\Form\%s%sType', ucfirst($name), end($arr));
        if (!class_exists($formClass)) {
            if (false !== $parentClass = get_parent_class($entity)) {
                $formClass = $this->getFormType($parentClass);
            } else {
                return;
            }
        }

        return $formClass;
    }
}
