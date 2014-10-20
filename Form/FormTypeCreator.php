<?php

namespace Knp\RadBundle\Form;

use Knp\RadBundle\Reflection\ClassMetadataFetcher;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormRegistryInterface;
use Knp\RadBundle\AppBundle\BundleGuesser;
use Doctrine\Common\Inflector\Inflector;

class FormTypeCreator implements FormCreatorInterface
{
    private $fetcher;
    private $factory;
    private $formRegistry;
    private $bundleGuesser;

    public function __construct(ClassMetadataFetcher $fetcher = null, FormFactoryInterface $factory, FormRegistryInterface $formRegistry, BundleGuesser $bundleGuesser)
    {
        $this->fetcher         = $fetcher ?: new ClassMetadataFetcher;
        $this->factory         = $factory;
        $this->formRegistry    = $formRegistry;
        $this->bundleGuesser   = $bundleGuesser;
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
        $bundle = $this->bundleGuesser->getBundleForClass($object);

        $id = sprintf('app.form.%s%s_type', $currentPurpose, strtolower($this->fetcher->getShortClassName($object)));
        $class = sprintf('%s\\Form\\%s%sType', $bundle->getNamespace(), Inflector::classify($purpose), $this->fetcher->getShortClassName($object));

        if (null === $type = $this->loadFormType($id, $class)) {
            $id = sprintf('app.form.type.%s%s_type', $currentPurpose, strtolower($this->fetcher->getShortClassName($object)));
            $class = sprintf('%s\\Form\\Type\\%s%sType', $bundle->getNamespace(), Inflector::classify($purpose), $this->fetcher->getShortClassName($object));

            $type = $this->loadFormType($id, $class);
        }

        if (null === $type && null !== $purpose) {
            // Let's try without purpose
            $type = $this->getFormType($object);
        }

        return $type;
    }

    private function loadFormType($id, $class)
    {
        $type = $this->getAlias($class, $id);

        if (!$this->formRegistry->hasType($type)) {

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
