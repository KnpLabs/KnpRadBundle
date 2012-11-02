<?php

namespace Knp\RadBundle\Form;

use Knp\RadBundle\Reflection\ClassMetadataFetcher;

class FormTypeCreator implements FormCreatorInterface
{
    public function __construct(ClassMetadataFetcher $fetcher = null)
    {
        $this->fetcher = $fetcher ?: new ClassMetadataFetcher;
    }

    public function create($object, $purpose = null, array $options = array())
    {
        $formClass = $this->getFormType($object, $purpose);

        if (null !== $formClass) {
            return $this->fetcher->newInstance($formClass, array($object, $options));
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
