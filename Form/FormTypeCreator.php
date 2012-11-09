<?php

namespace Knp\RadBundle\Form;

use Knp\RadBundle\Reflection\ClassMetadataFetcher;
use Symfony\Component\Form\FormFactoryInterface;

class FormTypeCreator implements FormCreatorInterface
{
    private $fetcher;
    private $factory;
    private $formRegistry;

    public function __construct(ClassMetadataFetcher $fetcher = null, FormFactoryInterface $factory, $formRegistry)
    {
        $this->fetcher = $fetcher ?: new ClassMetadataFetcher;
        $this->factory = $factory;
        $this->formRegistry = $formRegistry;
    }

    public function create($object, $purpose = null, array $options = array())
    {
        $type = $this->getFormType($object, $purpose);

        if (null !== $type) {
            return $this->factory->create($type, $object, $options);
        }
    }

    private function getFormType($object, $purpose = null)
    {
        $currentPurpose = $purpose ? $purpose.'_' : '';

        $id = sprintf('app_form_%s%s_type', $currentPurpose, strtolower($this->fetcher->getShortClassName($object)));
        $class = sprintf('App\\Form\\%s%sType', ucfirst($purpose), $this->fetcher->getShortClassName($object));
        $type = $this->getAlias($class, $id);

        if (!$this->formRegistry->hasType($type)) {
            if ($purpose) {
                return $this->getFormType($object);
            }

            return null;
        }

        return $type;
    }

    private function getAlias($class, $default)
    {
        if (!class_exists($class)) {
            return $default;
        }

        try {
            return unserialize(sprintf('O:%d:"%s":0:{}', strlen($class), $class))->getName();
        } catch (\Exception $e) {
        }

        return $default;
    }
}
