<?php

namespace Knp\RadBundle\Form;

use Knp\RadBundle\Reflection\ClassMetadataFetcher;
use Symfony\Component\Form\FormFactoryInterface;

class FormTypeCreator implements FormCreatorInterface
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
        $formClass = $this->getFormType($object, $purpose);

        if (null !== $formClass) {
            $formType = $this->fetcher->newInstance($formClass);

            return $this->factory->create($formType, $object, $options);
        }
    }

    private function getFormType($object, $purpose = null)
    {
        $objectClass = is_object($object) ? $this->fetcher->getClass($object) : $object;
        $arr         = explode('\\', $objectClass);
        $formClass   = sprintf('App\Form\%s%sType', ucfirst($purpose), end($arr));

        if (!$this->fetcher->classExists($formClass)) {
            if ($parentClass = $this->fetcher->getParentClass($object)) {
                return $this->getFormType($parentClass, $purpose);
            }
            return;
        }

        return $formClass;
    }
}
