<?php

namespace Knp\RadBundle\Form;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormEvent;;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Knp\RadBundle\Reflection\ClassMetadataFetcher;
use Doctrine\Common\Inflector\Inflector;

class DefaultFormCreator implements FormCreatorInterface, EventSubscriberInterface
{
    private $fetcher;
    private $factory;
    private $dataTypeGuesser;

    public function __construct(ClassMetadataFetcher $fetcher = null, FormFactoryInterface $factory, DataTypeGuesser $dataTypeGuesser)
    {
        $this->fetcher = $fetcher ?: new ClassMetadataFetcher;
        $this->factory = $factory;
        $this->dataTypeGuesser = $dataTypeGuesser;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData'
        );
    }

    public function create($object, $purpose = null, array $options = array())
    {
        $builder = $this->factory->createBuilder('form', $object, $options);
        $builder->addEventSubscriber($this);

        return $builder->getForm();
    }

    public function preSetData(FormEvent $event)
    {
        $object = $event->getData();
        $form = $event->getForm();

        if (!is_object($object)) {
            return;
        }

        $this->dataTypeGuesser->setData($object);

        foreach ($this->fetcher->getMethods($object) as $method) {
            if (0 === strpos($method, 'get') || 0 === strpos($method, 'is')) {
                $propertyName = $this->extractPropertyName($method);
                if ($this->hasRelatedSetter($object, $propertyName)) {
                    $form->add($this->factory->createForProperty(get_class($object), $propertyName, null, array(
                        'auto_initialize' => false,
                    )));
                }
            }
        }

        foreach ($this->fetcher->getProperties($object) as $property) {
            $form->add($this->factory->createForProperty(get_class($object), $property, null, array(
                'auto_initialize' => false,
            )));
        }
    }

    private function extractPropertyName($methodName)
    {
        return lcfirst(preg_replace('#is|get#', '', $methodName));
    }

    private function hasRelatedSetter($object, $propertyName)
    {
        return $this->fetcher->hasMethod($object, 'set'.Inflector::classify($propertyName));
    }
}
